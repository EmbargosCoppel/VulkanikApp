<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ClienteController extends Controller
{
    public function index(): JsonResponse
    {
        $clientes = Cliente::with('vehiculos')->get();
        return response()->json($clientes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
            'rfc' => 'nullable|string|max:13',
            'es_empresa' => 'sometimes|boolean',
            'nombre_empresa' => 'nullable|string|max:255',
        ]);

        $cliente = Cliente::create($validated);
        return response()->json($cliente, 201);
    }

    public function show(Cliente $cliente): JsonResponse
    {
        $cliente->load('vehiculos.ordenesTrabajo');
        return response()->json($cliente);
    }

    public function update(Request $request, Cliente $cliente): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|string|max:20',
            'email' => 'nullable|email|max:255',
            'direccion' => 'nullable|string',
            'rfc' => 'nullable|string|max:13',
            'es_empresa' => 'sometimes|boolean',
            'nombre_empresa' => 'nullable|string|max:255',
        ]);

        $cliente->update($validated);
        return response()->json($cliente);
    }

    public function destroy(Cliente $cliente): JsonResponse
    {
        $cliente->delete();
        return response()->json(null, 204);
    }
}
