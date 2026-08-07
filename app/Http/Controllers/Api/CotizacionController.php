<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Refaccion;
use App\Models\Vehiculo;
use App\Services\Builders\CotizacionBuilder;
use App\Services\Strategies\ClientePremiumStrategy;
use App\Services\Strategies\FlotillaStrategy;
use App\Services\Strategies\PublicoGeneralStrategy;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CotizacionController extends Controller
{
    private CotizacionBuilder $builder;

    public function __construct(CotizacionBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Generate a quote (cotización) for a work order.
     */
    public function generar(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vehiculo_id' => 'required|exists:vehiculos,id',
            'mano_obra' => 'required|numeric|min:0',
            'refacciones' => 'sometimes|array',
            'refacciones.*.id' => 'required|exists:refacciones,id',
            'refacciones.*.cantidad' => 'required|integer|min:1',
            'servicios' => 'sometimes|array',
            'servicios.*.nombre' => 'required|string|max:255',
            'servicios.*.precio' => 'required|numeric|min:0',
            'notas' => 'nullable|string',
            'tipo_cliente' => 'sometimes|in:publico,premium,flotilla',
        ]);

        $cliente = Cliente::findOrFail($validated['cliente_id']);
        $vehiculo = Vehiculo::findOrFail($validated['vehiculo_id']);

        $this->builder->reset();
        $this->builder->setCliente($cliente->nombre);
        $this->builder->setVehiculo($vehiculo->marca, $vehiculo->modelo, $vehiculo->placa);
        $this->builder->setManoObra($validated['mano_obra']);

        // Agregar refacciones
        foreach ($validated['refacciones'] ?? [] as $item) {
            $refaccion = Refaccion::findOrFail($item['id']);
            $this->builder->addRefaccion($refaccion, $item['cantidad']);
        }

        // Agregar servicios adicionales
        foreach ($validated['servicios'] ?? [] as $servicio) {
            $this->builder->addServicio($servicio['nombre'], $servicio['precio']);
        }

        if (!empty($validated['notas'])) {
            $this->builder->setNotas($validated['notas']);
        }

        // Seleccionar estrategia de precios según tipo de cliente
        $tipoCliente = $validated['tipo_cliente'] ?? 'publico';
        $strategy = match ($tipoCliente) {
            'premium' => new ClientePremiumStrategy(),
            'flotilla' => new FlotillaStrategy(),
            default => new PublicoGeneralStrategy(),
        };
        $this->builder->setPricingStrategy($strategy);

        $cotizacion = $this->builder->build();

        return response()->json($cotizacion);
    }

    /**
     * Get available pricing strategies.
     */
    public function getEstrategias(): JsonResponse
    {
        return response()->json([
            'estrategias' => [
                ['id' => 'publico', 'nombre' => 'Público General', 'descripcion' => 'Precio estándar sin descuentos'],
                ['id' => 'premium', 'nombre' => 'Cliente Premium', 'descripcion' => 'Descuento especial para clientes frecuentes'],
                ['id' => 'flotilla', 'nombre' => 'Flotilla', 'descripcion' => 'Descuento por volumen para empresas'],
            ],
        ]);
    }
}