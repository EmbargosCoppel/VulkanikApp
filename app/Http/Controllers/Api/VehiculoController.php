<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VehiculoController extends Controller
{
    public function index(): JsonResponse
    {
        $vehiculos = Vehiculo::with('cliente')->get();
        return response()->json($vehiculos);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'placa' => 'required|string|unique:vehiculos,placa',
            'color' => 'nullable|string|max:50',
            'vin' => 'nullable|string|max:17',
            'notas' => 'nullable|string',
        ]);

        $vehiculo = Vehiculo::create($validated);
        $vehiculo->load('cliente');
        return response()->json($vehiculo, 201);
    }

    public function show(Vehiculo $vehiculo): JsonResponse
    {
        $vehiculo->load(['cliente', 'ordenesTrabajo.mecanico', 'ordenesTrabajo.refacciones']);
        return response()->json($vehiculo);
    }

    public function update(Request $request, Vehiculo $vehiculo): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'sometimes|exists:clientes,id',
            'marca' => 'sometimes|string|max:100',
            'modelo' => 'sometimes|string|max:100',
            'anio' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'placa' => 'sometimes|string|unique:vehiculos,placa,' . $vehiculo->id,
            'color' => 'nullable|string|max:50',
            'vin' => 'nullable|string|max:17',
            'notas' => 'nullable|string',
        ]);

        $vehiculo->update($validated);
        return response()->json($vehiculo);
    }

    public function destroy(Vehiculo $vehiculo): JsonResponse
    {
        $vehiculo->delete();
        return response()->json(null, 204);
    }
}
