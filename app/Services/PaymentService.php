<?php

namespace App\Services;

use App\Models\OrdenTrabajo;
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
}
