<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenTrabajo;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    /**
     * Handle Stripe webhook events.
     * This endpoint is public (no auth) but validates the Stripe signature.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        // Verificar la firma de Stripe si está configurada
        if ($endpointSecret) {
            try {
                \Stripe\Webhook::constructEvent(
                    $payload, $sigHeader, $endpointSecret
                );
            } catch (\Exception $e) {
                Log::error('Webhook de Stripe: firma inválida', [
                    'error' => $e->getMessage(),
                ]);
                return response()->json(['error' => 'Firma inválida'], 400);
            }
        }

        $event = json_decode($payload, true);

        if (!isset($event['type'])) {
            return response()->json(['error' => 'Evento inválido'], 400);
        }

        Log::info('Webhook de Stripe recibido', [
            'type' => $event['type'],
        ]);

        switch ($event['type']) {
            case 'payment_intent.succeeded':
                $this->handlePaymentSucceeded($event['data']['object'] ?? []);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentFailed($event['data']['object'] ?? []);
                break;

            case 'charge.refunded':
                $this->handleRefunded($event['data']['object'] ?? []);
                break;

            default:
                // Evento no manejado, responder 200 para que Stripe no reintente
                break;
        }

        return response()->json(['received' => true]);
    }

    private function handlePaymentSucceeded(array $paymentIntent): void
    {
        $ordenId = $paymentIntent['metadata']['orden_id'] ?? null;

        if ($ordenId) {
            $orden = OrdenTrabajo::find($ordenId);

            if ($orden && !$orden->estaFinalizada()) {
                // Verificar si ya existe un pago para esta orden
                $pagoExistente = \App\Models\Pago::where('orden_trabajo_id', $ordenId)
                    ->where('transaction_id', $paymentIntent['id'])
                    ->first();

                if (!$pagoExistente) {
                    // Crear el registro de pago
                    \App\Models\Pago::create([
                        'orden_trabajo_id' => $ordenId,
                        'transaction_id' => $paymentIntent['id'],
                        'payment_method' => 'card',
                        'monto' => $paymentIntent['amount'] / 100, // Stripe usa centavos
                        'estado' => 'completado',
                        'moneda' => strtoupper($paymentIntent['currency'] ?? 'mxn'),
                        'metadata' => [
                            'payment_intent_status' => $paymentIntent['status'],
                            'orden_id' => $ordenId,
                            'cliente_email' => $paymentIntent['metadata']['cliente_email'] ?? null,
                        ],
                    ]);
                }

                // Marcar la orden como finalizada
                $orden->estado = 'finalizado';
                $orden->fecha_salida = now();
                $orden->save();

                Log::info('Orden finalizada por webhook de pago', [
                    'orden_id' => $ordenId,
                    'payment_intent' => $paymentIntent['id'],
                    'monto' => $paymentIntent['amount'] / 100,
                ]);
            }
        }
    }

    private function handlePaymentFailed(array $paymentIntent): void
    {
        $ordenId = $paymentIntent['metadata']['orden_id'] ?? null;

        if ($ordenId) {
            Log::warning('Pago fallido para orden', [
                'orden_id' => $ordenId,
                'payment_intent' => $paymentIntent['id'] ?? null,
            ]);
        }
    }

    private function handleRefunded(array $charge): void
    {
        $ordenId = $charge['metadata']['orden_id'] ?? null;

        if ($ordenId) {
            $orden = OrdenTrabajo::find($ordenId);

            if ($orden && $orden->estaFinalizada()) {
                $orden->estado = 'reparación';
                $orden->fecha_salida = null;
                $orden->save();

                Log::info('Orden revertida por reembolso', [
                    'orden_id' => $ordenId,
                    'charge_id' => $charge['id'] ?? null,
                ]);
            }
        }
    }
}