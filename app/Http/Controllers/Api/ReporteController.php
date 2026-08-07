<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\OrdenTrabajo;
use App\Models\Refaccion;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Get sales report (ventas) with optional date range.
     */
    public function ventas(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fecha_inicio' => 'sometimes|date',
            'fecha_fin' => 'sometimes|date|after_or_equal:fecha_inicio',
        ]);

        $query = OrdenTrabajo::where('estado', 'finalizado');

        if (!empty($validated['fecha_inicio'])) {
            $query->whereDate('fecha_salida', '>=', $validated['fecha_inicio']);
        }

        if (!empty($validated['fecha_fin'])) {
            $query->whereDate('fecha_salida', '<=', $validated['fecha_fin']);
        }

        $ventas = $query->get();

        $totalVentas = $ventas->sum('total');
        $totalRefacciones = $ventas->sum(function ($orden) {
            return $orden->refacciones->sum('pivot.subtotal');
        });
        $totalManoObra = $ventas->sum('mano_obra');
        $totalIVA = $ventas->sum('iva');

        // Ventas por mes
        $ventasPorMes = $query->select(
                DB::raw('YEAR(fecha_salida) as anio'),
                DB::raw('MONTH(fecha_salida) as mes'),
                DB::raw('SUM(total) as total'),
                DB::raw('COUNT(*) as cantidad')
            )
            ->groupBy('anio', 'mes')
            ->orderBy('anio', 'desc')
            ->orderBy('mes', 'desc')
            ->get();

        return response()->json([
            'resumen' => [
                'total_ventas' => $totalVentas,
                'total_refacciones' => $totalRefacciones,
                'total_mano_obra' => $totalManoObra,
                'total_iva' => $totalIVA,
                'numero_ordenes' => $ventas->count(),
            ],
            'ventas_por_mes' => $ventasPorMes,
        ]);
    }

    /**
     * Get orders report by status.
     */
    public function ordenesPorEstado(): JsonResponse
    {
        $ordenes = OrdenTrabajo::select('estado', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('estado')
            ->get();

        return response()->json($ordenes);
    }

    /**
     * Get most used refacciones (top 10).
     */
    public function refaccionesMasUsadas(): JsonResponse
    {
        $refacciones = Refaccion::withCount('ordenesTrabajo')
            ->where('activo', true)
            ->orderBy('ordenes_trabajo_count', 'desc')
            ->limit(10)
            ->get();

        return response()->json($refacciones);
    }

    /**
     * Get mechanics performance report.
     */
    public function rendimientoMecanicos(): JsonResponse
    {
        $mecanicos = User::where('role', 'mecanico')
            ->withCount([
                'ordenesAsignadas as total_ordenes',
                'ordenesAsignadas as ordenes_completadas' => function ($query) {
                    $query->where('estado', 'finalizado');
                },
                'ordenesAsignadas as ordenes_pendientes' => function ($query) {
                    $query->whereIn('estado', ['diagnóstico', 'esperando_piezas', 'reparación']);
                },
            ])
            ->withSum('ordenesAsignadas as total_ingresos', 'total')
            ->orderBy('total_ordenes', 'desc')
            ->get();

        return response()->json($mecanicos);
    }

    /**
     * Get client statistics.
     */
    public function clientes(): JsonResponse
    {
        $clientes = Cliente::withCount('vehiculos')
            ->withCount(['vehiculos as total_ordenes' => function ($query) {
                $query->whereHas('ordenesTrabajo');
            }])
            ->orderBy('total_ordenes', 'desc')
            ->limit(10)
            ->get();

        return response()->json($clientes);
    }

    /**
     * Get inventory report.
     */
    public function inventario(): JsonResponse
    {
        $refacciones = Refaccion::where('activo', true)->get();

        $valorInventario = $refacciones->sum(function ($r) {
            return $r->stock_actual * $r->costo;
        });

        $valorVenta = $refacciones->sum(function ($r) {
            return $r->stock_actual * $r->precio_venta;
        });

        $stockBajo = $refacciones->filter(function ($r) {
            return $r->estaBajoStock();
        });

        return response()->json([
            'total_refacciones' => $refacciones->count(),
            'valor_inventario_costo' => $valorInventario,
            'valor_inventario_venta' => $valorVenta,
            'stock_bajo' => $stockBajo->values(),
            'stock_bajo_count' => $stockBajo->count(),
        ]);
    }
}