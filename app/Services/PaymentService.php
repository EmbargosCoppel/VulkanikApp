<?php

namespace App\Services;

use App\Models\OrdenTrabajo;
use App\Models\Pago;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    private $paymentAdapter;

    public function __construct(PaymentAdapterInterface $paymentAdapter)
    {
        $this->paymentAdapter = $paymentAdapter;
    }

    public function procesarPago(OrdenTrabajo $orden, array $datosPago): array
    {
        if ($orden->estaFinalizada()) {
            throw new \InvalidArgumentException("La orden ya está finalizada y pagada");
        }

        return DB::transaction(function () use ($orden, $datosPago) {
            $resultadoPago = $this->paymentAdapter->procesarPago($orden->total, $datosPago);

            if ($resultadoPago['exitoso']) {
                // Guardar el pago en la base de datos
                Pago::create([
                    'orden_trabajo_id' => $orden->id,
                    'transaction_id' => $resultadoPago['transaction_id'],
                    'payment_method' => $datosPago['payment_method'] ?? 'card',
                    'monto' => $orden->total,
                    'estado' => 'completado',
                    'moneda' => config('taller.payment.currency', 'mxn'),
                    'metadata' => [
                        'payment_intent_status' => $resultadoPago['status'] ?? null,
                        'orden_id' => $orden->id,
                    ],
                ]);

                $orden->estado = 'finalizado';
                $orden->fecha_salida = now();
                $orden->save();
            }

            return $resultadoPago;
        });
    }

    public function reembolsarPago(OrdenTrabajo $orden): array
    {
        return $this->paymentAdapter->reembolsar($orden);
    }

    public function generarLinkPago(OrdenTrabajo $orden, string $emailCliente): array
    {
        if ($orden->estaFinalizada()) {
            throw new \InvalidArgumentException("La orden ya está finalizada y pagada");
        }

        $urlRetorno = route('ordenes.show', $orden);
        
        $datosPago = [
            'orden_id' => $orden->id,
            'descripcion' => "Pago de Orden #{$orden->id} - " . $orden->vehiculo->cliente->nombre,
            'cliente_email' => $emailCliente,
            'url_retorno' => $urlRetorno,
        ];

        return $this->paymentAdapter->generarLinkPago($orden->total, $datosPago);
    }
}
