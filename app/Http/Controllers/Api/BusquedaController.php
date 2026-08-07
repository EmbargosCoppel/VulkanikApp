<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\OrdenTrabajo;
use App\Models\Refaccion;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BusquedaController extends Controller
{
    /**
     * Global search across all entities.
     */
    public function buscar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => 'required|string|min:2|max:100',
        ]);

        $q = $validated['q'];

        // Buscar clientes
        $clientes = Cliente::where(function ($query) use ($q) {
            $query->where('nombre', 'like', "%{$q}%")
                ->orWhere('email', 'like', "%{$q}%")
                ->orWhere('telefono', 'like', "%{$q}%")
                ->orWhere('rfc', 'like', "%{$q}%")
                ->orWhere('nombre_empresa', 'like', "%{$q}%");
        })->limit(5)->get();

        // Buscar vehículos
        $vehiculos = Vehiculo::where(function ($query) use ($q) {
            $query->where('marca', 'like', "%{$q}%")
                ->orWhere('modelo', 'like', "%{$q}%")
                ->orWhere('placa', 'like', "%{$q}%")
                ->orWhere('vin', 'like', "%{$q}%");
        })->with('cliente:id,nombre')->limit(5)->get();

        // Buscar órdenes de trabajo
        $ordenes = OrdenTrabajo::where(function ($query) use ($q) {
            $query->where('estado', 'like', "%{$q}%")
                ->orWhere('diagnostico', 'like', "%{$q}%")
                ->orWhere('trabajos_realizados', 'like', "%{$q}%")
                ->orWhereHas('vehiculo', function ($vq) use ($q) {
                    $vq->where('placa', 'like', "%{$q}%")
                        ->orWhere('marca', 'like', "%{$q}%")
                        ->orWhere('modelo', 'like', "%{$q}%");
                })
                ->orWhereHas('vehiculo.cliente', function ($cq) use ($q) {
                    $cq->where('nombre', 'like', "%{$q}%");
                });
        })->with(['vehiculo.cliente:id,nombre', 'mecanico:id,name'])->limit(5)->get();

        // Buscar refacciones
        $refacciones = Refaccion::where(function ($query) use ($q) {
            $query->where('nombre', 'like', "%{$q}%")
                ->orWhere('sku', 'like', "%{$q}%")
                ->orWhere('descripcion', 'like', "%{$q}%")
                ->orWhere('proveedor', 'like', "%{$q}%");
        })->where('activo', true)->limit(5)->get();

        return response()->json([
            'clientes' => $clientes,
            'vehiculos' => $vehiculos,
            'ordenes' => $ordenes,
            'refacciones' => $refacciones,
            'total' => $clientes->count() + $vehiculos->count() + $ordenes->count() + $refacciones->count(),
        ]);
    }
}