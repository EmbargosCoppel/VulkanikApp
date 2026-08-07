<?php

namespace App\Http\Controllers;

use App\Http\Requests\RefaccionRequest;
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

    public function store(RefaccionRequest $request)
    {
        $this->inventoryService->crearRefaccion($request->validated());
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

    public function update(RefaccionRequest $request, Refaccion $refaccion)
    {
        $refaccion->update($request->validated());
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
