<?php

namespace App\Services;

use App\Models\OrdenTrabajo;
use App\Models\Refaccion;
use Illuminate\Support\Facades\DB;

class WorkOrderService
{
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
        if (!$this->puedeCambiarA($orden->estado, $nuevoEstado)) {
            throw new \InvalidArgumentException("No se puede cambiar de {$orden->estado} a {$nuevoEstado}");
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
        if (!$orden->puedeAgregarRefacciones()) {
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
        $iva = $subtotal * 0.16;
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

    private function puedeCambiarA(string $estadoActual, string $nuevoEstado): bool
    {
        $transicionesValidas = [
            'diagnóstico' => ['esperando_piezas', 'reparación', 'finalizado'],
            'esperando_piezas' => ['reparación', 'finalizado'],
            'reparación' => ['finalizado'],
            'finalizado' => [],
        ];

        return in_array($nuevoEstado, $transicionesValidas[$estadoActual] ?? []);
    }
}
