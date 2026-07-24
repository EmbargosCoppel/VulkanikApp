<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Refaccion;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class RefaccionController extends Controller
{
    private InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(): JsonResponse
    {
        $refacciones = Refaccion::where('activo', true)->get();
        return response()->json($refacciones);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'sku' => 'required|string|unique:refacciones,sku',
            'descripcion' => 'nullable|string',
            'costo' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'proveedor' => 'nullable|string|max:255',
        ]);

        $refaccion = $this->inventoryService->crearRefaccion($validated);
        return response()->json($refaccion, 201);
    }

    public function show(Refaccion $refaccion): JsonResponse
    {
        return response()->json($refaccion);
    }

    public function update(Request $request, Refaccion $refaccion): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => 'sometimes|string|max:255',
            'sku' => 'sometimes|string|unique:refacciones,sku,' . $refaccion->id,
            'descripcion' => 'nullable|string',
            'costo' => 'sometimes|numeric|min:0',
            'precio_venta' => 'sometimes|numeric|min:0',
            'stock_actual' => 'sometimes|integer|min:0',
            'stock_minimo' => 'sometimes|integer|min:0',
            'ubicacion' => 'nullable|string|max:255',
            'proveedor' => 'nullable|string|max:255',
            'activo' => 'sometimes|boolean',
        ]);

        $refaccion->update($validated);
        return response()->json($refaccion);
    }

    public function destroy(Refaccion $refaccion): JsonResponse
    {
        $refaccion->activo = false;
        $refaccion->save();
        return response()->json(null, 204);
    }

    public function actualizarStock(Request $request, Refaccion $refaccion): JsonResponse
    {
        $validated = $request->validate([
            'nuevo_stock' => 'required|integer|min:0',
        ]);

        $refaccion = $this->inventoryService->actualizarStock($refaccion, $validated['nuevo_stock']);
        return response()->json($refaccion);
    }

    public function stockBajo(): JsonResponse
    {
        $refacciones = $this->inventoryService->obtenerRefaccionesBajoStock();
        return response()->json($refacciones);
    }
}
