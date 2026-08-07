<?php

namespace App\Services;

use App\Models\OrdenTrabajo;
use App\Models\Refaccion;
use App\Services\PaymentService;
use App\Services\States\DiagnosticoState;
use App\Services\States\EsperandoPiezasState;
use App\Services\States\FinalizadoState;
use App\Services\States\OrdenStateInterface;
use App\Services\States\ReparacionState;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
    private array $stateMap;
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        $this->stateMap = [
            'diagnóstico' => new DiagnosticoState(),
            'esperando_piezas' => new EsperandoPiezasState(),
            'reparación' => new ReparacionState(),
            'finalizado' => new FinalizadoState(),
        ];
    }

    public function getState(string $estado): OrdenStateInterface
    {
        return $this->stateMap[$estado] ?? throw new \InvalidArgumentException("Estado inválido: {$estado}");
    }

    public function crearOrden(array $datos): OrdenTrabajo
    {
        return DB::transaction(function () use ($datos) {
            $orden = OrdenTrabajo::create([
                'vehiculo_id' => $datos['vehiculo_id'],
                'mecanico_id' => $datos['mecanico_id'],
                'estado' => 'diagnóstico',
                'fecha_entrada' => now(),
                'diagnostico' => $datos['diagnostico'] ?? null,
            ]);

            return $orden;
        });
    }

    public function actualizarEstado(OrdenTrabajo $orden, string $nuevoEstado): OrdenTrabajo
    {
        $stateActual = $this->getState($orden->estado);
        $nuevoState = $this->getState($nuevoEstado);

        if (!$stateActual->puedeCambiarEstado()) {
            throw new \InvalidArgumentException("La orden en estado {$orden->estado} no puede cambiar de estado");
        }

        if ($stateActual->siguienteEstado() !== $nuevoEstado && $nuevoEstado !== 'finalizado') {
            $transicionesValidas = $this->obtenerTransicionesValidas($orden->estado);
            if (!in_array($nuevoEstado, $transicionesValidas)) {
                throw new \InvalidArgumentException("No se puede cambiar de {$orden->estado} a {$nuevoEstado}");
            }
        }

        $orden->estado = $nuevoEstado;

        if ($nuevoEstado === 'finalizado') {
            $orden->fecha_salida = now();
        }

        $orden->save();
        return $orden;
    }

    public function agregarRefaccion(OrdenTrabajo $orden, Refaccion $refaccion, int $cantidad): void
    {
        $state = $this->getState($orden->estado);

        if (!$state->puedeAgregarRefacciones()) {
            throw new \InvalidArgumentException("No se pueden agregar refacciones a una orden en estado {$orden->estado}");
        }

        if ($refaccion->stock_actual < $cantidad) {
            throw new \InvalidArgumentException("Stock insuficiente. Disponible: {$refaccion->stock_actual}, Solicitado: {$cantidad}");
        }

        DB::transaction(function () use ($orden, $refaccion, $cantidad) {
            $precioUnitario = $refaccion->precio_venta;
            $subtotal = $precioUnitario * $cantidad;

            $orden->refacciones()->attach($refaccion->id, [
                'cantidad' => $cantidad,
                'precio_unitario' => $precioUnitario,
                'subtotal' => $subtotal,
            ]);

            $refaccion->decrement('stock_actual', $cantidad);

            $this->recalcularTotales($orden);
        });
    }

    public function calcularTotales(OrdenTrabajo $orden): array
    {
        $subtotalRefacciones = $orden->refacciones->sum('pivot.subtotal');
        $subtotal = $subtotalRefacciones + $orden->mano_obra;
        $iva = $subtotal * config('taller.iva', 0.16);
        $total = $subtotal + $iva;

        return [
            'subtotal_refacciones' => $subtotalRefacciones,
            'mano_obra' => $orden->mano_obra,
            'subtotal' => $subtotal,
            'iva' => $iva,
            'total' => $total,
        ];
    }

    public function recalcularTotales(OrdenTrabajo $orden): void
    {
        $totales = $this->calcularTotales($orden);

        $orden->update([
            'subtotal' => $totales['subtotal'],
            'iva' => $totales['iva'],
            'total' => $totales['total'],
        ]);
    }

    public function obtenerTransicionesValidas(string $estadoActual): array
    {
        $transiciones = [
            'diagnóstico' => ['esperando_piezas', 'reparación', 'finalizado'],
            'esperando_piezas' => ['reparación', 'finalizado'],
            'reparación' => ['finalizado'],
            'finalizado' => [],
        ];

        return $transiciones[$estadoActual] ?? [];
    }

    public function procesarPago(OrdenTrabajo $orden, array $datosPago): array
    {
        return $this->paymentService->procesarPago($orden, $datosPago);
    }
}
