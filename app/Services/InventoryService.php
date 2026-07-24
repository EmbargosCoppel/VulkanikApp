<?php

namespace App\Services;

use App\Models\Refaccion;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function crearRefaccion(array $datos): Refaccion
    {
        return Refaccion::create($datos);
    }

    public function actualizarStock(Refaccion $refaccion, int $nuevoStock): Refaccion
    {
        $refaccion->stock_actual = $nuevoStock;
        $refaccion->save();
        
        if ($refaccion->estaBajoStock()) {
            event(new \App\Events\StockBajo($refaccion, $refaccion->stock_actual, $refaccion->stock_minimo));
        }
        
        return $refaccion;
    }

    public function incrementarStock(Refaccion $refaccion, int $cantidad): Refaccion
    {
        return $this->actualizarStock($refaccion, $refaccion->stock_actual + $cantidad);
    }

    public function decrementarStock(Refaccion $refaccion, int $cantidad): Refaccion
    {
        if ($refaccion->stock_actual < $cantidad) {
            throw new \InvalidArgumentException("Stock insuficiente");
        }

        return $this->actualizarStock($refaccion, $refaccion->stock_actual - $cantidad);
    }

    public function obtenerRefaccionesBajoStock(): \Illuminate\Database\Eloquent\Collection
    {
        return Refaccion::whereColumn('stock_actual', '<=', 'stock_minimo')
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->orderBy('stock_actual', 'asc')
            ->get();
    }

    public function obtenerHistorialMovimientos(Refaccion $refaccion): array
    {
        // Implementación futura para tracking de movimientos
        return [];
    }
}
