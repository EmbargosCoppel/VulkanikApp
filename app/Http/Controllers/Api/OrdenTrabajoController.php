<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajo;
use App\Models\Refaccion;
use App\Services\WorkOrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrdenTrabajoController extends Controller
{
    private WorkOrderService $workOrderService;

    public function __construct(WorkOrderService $workOrderService)
    {
        $this->workOrderService = $workOrderService;
    }

    public function index(): JsonResponse
    {
        $ordenes = OrdenTrabajo::with(['vehiculo.cliente', 'mecanico', 'refacciones'])->get();
        return response()->json($ordenes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'mecanico_id' => 'required|exists:users,id',
            'diagnostico' => 'nullable|string',
        ]);

        $orden = $this->workOrderService->crearOrden($validated);
        return response()->json($orden, 201);
    }

    public function show(OrdenTrabajo $ordenTrabajo): JsonResponse
    {
        $ordenTrabajo->load(['vehiculo.cliente', 'mecanico', 'refacciones']);
        return response()->json($ordenTrabajo);
    }

    public function update(Request $request, OrdenTrabajo $ordenTrabajo): JsonResponse
    {
        $validated = $request->validate([
            'estado' => 'sometimes|in:diagnóstico,esperando_piezas,reparación,finalizado',
            'diagnostico' => 'nullable|string',
            'trabajos_realizados' => 'nullable|string',
            'mano_obra' => 'sometimes|numeric|min:0',
            'observaciones' => 'nullable|string',
        ]);

        if (isset($validated['estado'])) {
            $ordenTrabajo = $this->workOrderService->actualizarEstado($ordenTrabajo, $validated['estado']);
            unset($validated['estado']);
        }

        $ordenTrabajo->update($validated);
        return response()->json($ordenTrabajo);
    }

    public function agregarRefaccion(Request $request, OrdenTrabajo $ordenTrabajo): JsonResponse
    {
        $validated = $request->validate([
            'refaccion_id' => 'required|exists:refacciones,id',
            'cantidad' => 'required|integer|min:1',
        ]);

        $refaccion = Refaccion::findOrFail($validated['refaccion_id']);
        $this->workOrderService->agregarRefaccion($ordenTrabajo, $refaccion, $validated['cantidad']);

        $ordenTrabajo->load('refacciones');
        return response()->json($ordenTrabajo);
    }

    public function calcularTotales(OrdenTrabajo $ordenTrabajo): JsonResponse
    {
        $totales = $this->workOrderService->calcularTotales($ordenTrabajo);
        return response()->json($totales);
    }
}
