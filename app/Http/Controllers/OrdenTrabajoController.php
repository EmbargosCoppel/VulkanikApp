<?php

namespace App\Http\Controllers;

use App\Models\OrdenTrabajo;
use App\Models\Vehiculo;
use App\Models\User;
use App\Services\WorkOrderService;
use Illuminate\Http\Request;

class OrdenTrabajoController extends Controller
{
    private WorkOrderService $workOrderService;

    public function __construct(WorkOrderService $workOrderService)
    {
        $this->workOrderService = $workOrderService;
    }

    public function index(Request $request)
    {
        $query = OrdenTrabajo::with(['vehiculo.cliente', 'mecanico']);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('estado', 'like', '%' . $request->search . '%')
                  ->orWhereHas('vehiculo', function ($vq) use ($request) {
                      $vq->where('placa', 'like', '%' . $request->search . '%')
                         ->orWhere('marca', 'like', '%' . $request->search . '%')
                         ->orWhere('modelo', 'like', '%' . $request->search . '%');
                  })
                  ->orWhereHas('vehiculo.cliente', function ($cq) use ($request) {
                      $cq->where('nombre', 'like', '%' . $request->search . '%');
                  });
            });
        }

        $ordenes = $query->orderBy('created_at', 'desc')
            ->paginate(config('taller.pagination.per_page', 15));
        return view('ordenes.index', compact('ordenes'));
    }

    public function create()
    {
        $vehiculos = Vehiculo::with('cliente')->get();
        $mecanicos = User::all();
        return view('ordenes.create', compact('vehiculos', 'mecanicos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'mecanico_id' => 'required|exists:users,id',
            'diagnostico' => 'nullable|string',
        ]);

        $this->workOrderService->crearOrden($validated);
        return redirect()->route('ordenes.index')->with('success', 'Orden de trabajo creada exitosamente');
    }

    public function show(OrdenTrabajo $ordenTrabajo)
    {
        $ordenTrabajo->load(['vehiculo.cliente', 'mecanico', 'refacciones']);
        $totales = $this->workOrderService->calcularTotales($ordenTrabajo);
        return view('ordenes.show', compact('ordenTrabajo', 'totales'));
    }

    public function edit(OrdenTrabajo $ordenTrabajo)
    {
        $ordenTrabajo->load('vehiculo.cliente');
        $mecanicos = User::all();
        return view('ordenes.edit', compact('ordenTrabajo', 'mecanicos'));
    }

    public function update(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        $validated = $request->validate([
            'estado' => 'sometimes|in:diagnóstico,esperando_piezas,reparación,finalizado',
            'mecanico_id' => 'sometimes|exists:users,id',
            'diagnostico' => 'nullable|string',
            'trabajos_realizados' => 'nullable|string',
            'mano_obra' => 'sometimes|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        // Actualizar estado primero si se proporciona
        if (isset($validated['estado'])) {
            try {
                $ordenTrabajo = $this->workOrderService->actualizarEstado($ordenTrabajo, $validated['estado']);
            } catch (\InvalidArgumentException $e) {
                return redirect()->route('ordenes.index')
                    ->with('error', 'No se puede cambiar el estado: ' . $e->getMessage());
            }
            unset($validated['estado']);
        }

        // Actualizar otros campos
        if (!empty($validated)) {
            $ordenTrabajo->update($validated);

            // Recalcular totales si cambió la mano de obra
            if (isset($validated['mano_obra'])) {
                $this->workOrderService->recalcularTotales($ordenTrabajo);
            }
        }

        return redirect()->route('ordenes.index')->with('success', 'Orden de trabajo actualizada exitosamente');
    }

    public function agregarRefaccion(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        $validated = $request->validate([
            'refaccion_id' => 'required|exists:refacciones,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $refaccion = \App\Models\Refaccion::findOrFail($validated['refaccion_id']);

        try {
            $this->workOrderService->agregarRefaccion($ordenTrabajo, $refaccion, $validated['cantidad']);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('ordenes.show', $ordenTrabajo)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('ordenes.show', $ordenTrabajo)->with('success', 'Refacción agregada exitosamente');
    }
}
