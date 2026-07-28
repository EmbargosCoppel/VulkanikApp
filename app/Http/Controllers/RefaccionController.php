<?php

namespace App\Http\Controllers;

use App\Models\Refaccion;
use App\Services\InventoryService;
use Illuminate\Http\Request;

class RefaccionController extends Controller
{
    private InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(Request $request)
    {
        $query = Refaccion::where('activo', true)
            ->withCount('ordenesTrabajo');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nombre', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%')
                  ->orWhere('proveedor', 'like', '%' . $request->search . '%');
            });
        }

        $refacciones = $query->orderBy('nombre')
            ->paginate(config('taller.pagination.per_page', 15));
        return view('refacciones.index', compact('refacciones'));
    }

    public function create()
    {
        return view('refacciones.create');
    }

    public function store(Request $request)
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

        $this->inventoryService->crearRefaccion($validated);
        return redirect()->route('refacciones.index')->with('success', 'Refacción creada exitosamente');
    }

    public function show(Refaccion $refaccion)
    {
        return view('refacciones.show', compact('refaccion'));
    }

    public function edit(Refaccion $refaccion)
    {
        return view('refacciones.edit', compact('refaccion'));
    }

    public function update(Request $request, Refaccion $refaccion)
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
        return redirect()->route('refacciones.index')->with('success', 'Refacción actualizada exitosamente');
    }

    public function destroy(Refaccion $refaccion)
    {
        $refaccion->delete();
        return redirect()->route('refacciones.index')->with('success', 'Refacción eliminada exitosamente');
    }

    public function stock(Refaccion $refaccion)
    {
        return view('refacciones.stock', compact('refaccion'));
    }

    public function actualizarStock(Request $request, Refaccion $refaccion)
    {
        $validated = $request->validate([
            'nuevo_stock' => 'required|integer|min:0',
        ]);

        $this->inventoryService->actualizarStock($refaccion, $validated['nuevo_stock']);
        return redirect()->route('refacciones.index')->with('success', 'Stock actualizado exitosamente');
    }

    public function stockBajo()
    {
        $refacciones = $this->inventoryService->obtenerRefaccionesBajoStock();
        return view('refacciones.stock-bajo', compact('refacciones'));
    }
}
