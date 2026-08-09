<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrdenTrabajoRequest;
use App\Http\Requests\RefaccionRequest;
use App\Models\OrdenTrabajo;
use App\Models\Vehiculo;
use App\Models\User;
use App\Models\Refaccion;
use App\Services\WorkOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrdenTrabajoController extends Controller
{
    private WorkOrderService $workOrderService;

    public function __construct(WorkOrderService $workOrderService)
    {
        $this->workOrderService = $workOrderService;
    }

    private function authorizeOrden(OrdenTrabajo $ordenTrabajo): void
    {
        if (auth()->user()->role === 'mecanico' && $ordenTrabajo->mecanico_id !== auth()->id()) {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $query = OrdenTrabajo::with(['vehiculo.cliente', 'mecanico']);

        if (auth()->user()->role === 'mecanico') {
            $query->where('mecanico_id', auth()->id());
        }

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
        if (auth()->user()->role === 'mecanico') {
            abort(403);
        }

        $vehiculos = Vehiculo::with('cliente')->get();
        $mecanicos = User::where('role', 'mecanico')->orderBy('name')->get();
        return view('ordenes.create', compact('vehiculos', 'mecanicos'));
    }

    public function store(OrdenTrabajoRequest $request)
    {
        if (auth()->user()->role === 'mecanico') {
            abort(403);
        }

        $this->workOrderService->crearOrden($request->validated());
        return redirect()->route('ordenes.index')->with('success', 'Orden de trabajo creada exitosamente');
    }

    public function show(OrdenTrabajo $ordenTrabajo)
    {
        $this->authorizeOrden($ordenTrabajo);

        $ordenTrabajo->load(['vehiculo.cliente', 'mecanico', 'refacciones']);
        $totales = $this->workOrderService->calcularTotales($ordenTrabajo);
        return view('ordenes.show', compact('ordenTrabajo', 'totales'));
    }

    public function edit(OrdenTrabajo $ordenTrabajo)
    {
        $this->authorizeOrden($ordenTrabajo);
        $ordenTrabajo->load('vehiculo.cliente');
        $mecanicos = User::where('role', 'mecanico')->orderBy('name')->get();
        return view('ordenes.edit', compact('ordenTrabajo', 'mecanicos'));
    }

    public function update(OrdenTrabajoRequest $request, OrdenTrabajo $ordenTrabajo)
    {
        $this->authorizeOrden($ordenTrabajo);

        $validated = $request->validated();

        if (auth()->user()->role === 'mecanico') {
            // Los mecánicos pueden actualizar: estado, diagnostico, mano_obra, trabajos_realizados
            $allowedFields = ['estado', 'diagnostico', 'mano_obra', 'trabajos_realizados'];
            $validated = array_intersect_key($validated, array_flip($allowedFields));
        }

        // Actualizar estado primero si se proporciona
        if (isset($validated['estado'])) {
            // Validar que solo el admin pueda cambiar a finalizado
            if ($validated['estado'] === 'finalizado' && auth()->user()->role !== 'admin') {
                return redirect()->route('ordenes.show', $ordenTrabajo)
                    ->with('error', 'Solo el administrador puede marcar la orden como finalizada');
            }
            
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
            // Normalize mano_obra to float if provided (accept comma or dot decimal)
            if (array_key_exists('mano_obra', $validated)) {
                $raw = $validated['mano_obra'];
                // Allow empty strings to be treated as zero
                if ($raw === '' || $raw === null) {
                    $validated['mano_obra'] = 0.0;
                } else {
                    $normalized = str_replace([',', ' '], ['.', ''], (string) $raw);
                    $validated['mano_obra'] = (float) $normalized;
                }
            }

            $ordenTrabajo->update($validated);

            // Ensure model has fresh values before recalculating
            $ordenTrabajo->refresh();

            // Recalcular totales si cambió la mano de obra o las refacciones
            if (array_key_exists('mano_obra', $validated) || array_key_exists('diagnostico', $validated) || array_key_exists('trabajos_realizados', $validated)) {
                $this->workOrderService->recalcularTotales($ordenTrabajo);
            }
        }

        // If the form explicitly requested to redirect to the order's show page, do that.
        if ($request->input('redirect_to') === 'show') {
            return redirect()->route('ordenes.show', $ordenTrabajo)->with('success', 'Orden de trabajo actualizada exitosamente');
        }

        return redirect()->route('ordenes.index')->with('success', 'Orden de trabajo actualizada exitosamente');
    }

    public function agregarRefaccion(\App\Http\Requests\AgregarRefaccionRequest $request, OrdenTrabajo $ordenTrabajo)
    {
        $this->authorizeOrden($ordenTrabajo);

        $validated = $request->validated();

        $refaccion = \App\Models\Refaccion::findOrFail($validated['refaccion_id']);

        try {
            $this->workOrderService->agregarRefaccion($ordenTrabajo, $refaccion, $validated['cantidad']);
        } catch (\InvalidArgumentException $e) {
            return redirect()->route('ordenes.show', $ordenTrabajo)
                ->with('error', $e->getMessage());
        }

        return redirect()->route('ordenes.show', $ordenTrabajo)->with('success', 'Refacción agregada exitosamente');
    }

    public function eliminarRefaccion(Request $request, OrdenTrabajo $ordenTrabajo, Refaccion $refaccion)
    {
        $this->authorizeOrden($ordenTrabajo);

        if ($ordenTrabajo->estaFinalizada()) {
            return redirect()->route('ordenes.show', $ordenTrabajo)
                ->with('error', 'No se puede modificar una orden finalizada');
        }

        try {
            // Obtener la cantidad de la refacción en la orden
            $cantidad = $ordenTrabajo->refacciones()->where('refaccion_id', $refaccion->id)->first()?->pivot->cantidad ?? 0;
            
            // Remover la relación
            $ordenTrabajo->refacciones()->detach($refaccion->id);
            
            // Devolver el stock
            if ($cantidad > 0) {
                $refaccion->increment('stock_actual', $cantidad);
            }
            
            // Recalcular totales
            $this->workOrderService->recalcularTotales($ordenTrabajo);
            
            return redirect()->route('ordenes.show', $ordenTrabajo)->with('success', 'Refacción eliminada exitosamente');
        } catch (\Exception $e) {
            \Log::error('Error al eliminar refacción', [
                'orden_id' => $ordenTrabajo->id,
                'refaccion_id' => $refaccion->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('ordenes.show', $ordenTrabajo)
                ->with('error', 'Error al eliminar la refacción: ' . $e->getMessage());
        }
    }

    public function ticket(OrdenTrabajo $ordenTrabajo)
    {
        $this->authorizeOrden($ordenTrabajo);

        $ordenTrabajo->load(['vehiculo.cliente', 'mecanico', 'refacciones']);
        $totales = $this->workOrderService->calcularTotales($ordenTrabajo);
        return view('ordenes.ticket', compact('ordenTrabajo', 'totales'));
    }

    public function kanban()
    {
        $ordenes = OrdenTrabajo::with(['vehiculo.cliente', 'mecanico'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('estado');

        // Asegurar que todas las columnas Kanban existan, incluso si están vacías
        $estados = ['diagnóstico', 'esperando_piezas', 'reparación', 'finalizado'];
        foreach ($estados as $estado) {
            if (!isset($ordenes[$estado])) {
                $ordenes[$estado] = collect([]);
            }
        }

        return view('ordenes.kanban', compact('ordenes'));
    }

    public function pagar(OrdenTrabajo $ordenTrabajo)
    {
        $this->authorizeOrden($ordenTrabajo);

        // Solo administradores pueden procesar pagos desde la interfaz web
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Solo administradores pueden procesar pagos');
        }

        if ($ordenTrabajo->estaFinalizada()) {
            return redirect()->route('ordenes.show', $ordenTrabajo)
                ->with('error', 'La orden ya está finalizada y pagada');
        }

        try {
            $ordenTrabajo->load(['vehiculo.cliente', 'refacciones']);
            
            // Asegurar que los totales estén calculados
            if (!$ordenTrabajo->total || $ordenTrabajo->total <= 0) {
                $this->workOrderService->recalcularTotales($ordenTrabajo);
                $ordenTrabajo->refresh();
            }
            
            $totales = $this->workOrderService->calcularTotales($ordenTrabajo);
            
            // Validar que el total sea mayor a 0
            if ($totales['total'] <= 0) {
                return redirect()->route('ordenes.show', $ordenTrabajo)
                    ->with('error', 'La orden no tiene un total válido para procesar el pago. Verifique que tenga refacciones o mano de obra asignada.');
            }
            
            return view('ordenes.pagar', compact('ordenTrabajo', 'totales'));
        } catch (\Exception $e) {
            \Log::error('Error al cargar página de pago', [
                'orden_id' => $ordenTrabajo->id,
                'error' => $e->getMessage(),
            ]);
            
            return redirect()->route('ordenes.show', $ordenTrabajo)
                ->with('error', 'Error al cargar la página de pago: ' . $e->getMessage());
        }
    }

    public function procesarPago(Request $request, OrdenTrabajo $ordenTrabajo)
    {
        $this->authorizeOrden($ordenTrabajo);

        // Refuerzo: solo admins pueden procesar pagos
        if (auth()->user()->role !== 'admin') {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['exitoso' => false, 'error' => 'Sin permiso para procesar pagos'], 403);
            }

            abort(403, 'Solo administradores pueden procesar pagos');
        }

        $wantsJson = $request->expectsJson() || $request->ajax();

        if ($ordenTrabajo->estaFinalizada()) {
            if ($wantsJson) {
                return response()->json([
                    'exitoso' => false,
                    'error' => 'La orden ya está finalizada y pagada',
                ], 422);
            }

            return redirect()->route('ordenes.show', $ordenTrabajo)
                ->with('error', 'La orden ya está finalizada y pagada');
        }

        $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        try {
            $resultado = $this->workOrderService->procesarPago($ordenTrabajo, [
                'payment_method_id' => $request->payment_method_id,
                'payment_method' => 'card',
                'orden_id' => $ordenTrabajo->id,
            ]);

            if ($resultado['exitoso']) {
                if ($wantsJson) {
                    return response()->json([
                        'exitoso' => true,
                        'mensaje' => 'Pago procesado exitosamente',
                        'redirect' => route('ordenes.ticket', $ordenTrabajo),
                    ]);
                }

                return redirect()->route('ordenes.ticket', $ordenTrabajo)
                    ->with('success', 'Pago procesado exitosamente');
            }

            if ($wantsJson) {
                return response()->json([
                    'exitoso' => false,
                    'error' => $resultado['error'] ?? 'Error al procesar el pago',
                ], 422);
            }

            return redirect()->route('ordenes.pagar', $ordenTrabajo)
                ->with('error', $resultado['error'] ?? 'Error al procesar el pago');
        } catch (\Exception $e) {
            if ($wantsJson) {
                return response()->json([
                    'exitoso' => false,
                    'error' => $e->getMessage(),
                ], 500);
            }

            return redirect()->route('ordenes.pagar', $ordenTrabajo)
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}
