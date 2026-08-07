<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajo;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Process a payment for a work order.
     */
    public function procesarPago(Request $request, OrdenTrabajo $ordenTrabajo): JsonResponse
    {
        $validated = $request->validate([
            'payment_method_id' => 'required|string',
            'amount' => 'sometimes|numeric|min:0',
        ]);

        try {
            $resultado = $this->paymentService->procesarPago($ordenTrabajo, $validated);

            return response()->json([
                'success' => $resultado['exitoso'],
                'transaction_id' => $resultado['transaction_id'] ?? null,
                'message' => $resultado['mensaje'] ?? 'Pago procesado',
                'orden' => $ordenTrabajo->fresh()->load(['vehiculo.cliente', 'refacciones']),
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el pago: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Refund a payment for a work order.
     */
    public function reembolsar(OrdenTrabajo $ordenTrabajo): JsonResponse
    {
        try {
            $resultado = $this->paymentService->reembolsarPago($ordenTrabajo);

            return response()->json([
                'success' => $resultado['exitoso'],
                'refund_id' => $resultado['refund_id'] ?? null,
                'message' => $resultado['mensaje'] ?? 'Reembolso procesado',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar el reembolso: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Stripe public key for frontend.
     */
    public function getConfig(): JsonResponse
    {
        $adapter = app(\App\Services\PaymentAdapterInterface::class);

        return response()->json([
            'public_key' => method_exists($adapter, 'getPublicKey') ? $adapter->getPublicKey() : null,
            'currency' => config('taller.payment.currency', 'mxn'),
        ]);
    }
}