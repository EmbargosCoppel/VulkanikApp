<?php

namespace App\Services\Adapters;

use App\Services\PaymentAdapterInterface;
use Illuminate\Support\Facades\Log;

class StripePaymentAdapter implements PaymentAdapterInterface
{
    private string $secretKey;
    private string $publicKey;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret');
        $this->publicKey = config('services.stripe.key');
    }

    public function procesarPago(float $monto, array $datosPago): array
    {
        try {
            // Implementación futura con SDK de Stripe
            // $stripe = new \Stripe\StripeClient($this->secretKey);
            // $paymentIntent = $stripe->paymentIntents->create([
            //     'amount' => $monto * 100, // Stripe usa centavos
            //     'currency' => 'mxn',
            //     'payment_method' => $datosPago['payment_method_id'],
            //     'confirm' => true,
            // ]);

            // Simulación para desarrollo(Solo simula no hace nada aun,solo es visial por ahora)
            Log::info('Procesando pago con Stripe', [
                'monto' => $monto,
                'datos_pago' => $datosPago,
            ]);

            return [
                'exitoso' => true,
                'transaction_id' => 'pi_' . uniqid(),
                'mensaje' => 'Pago procesado exitosamente',
            ];

        } catch (\Exception $e) {
            Log::error('Error al procesar pago con Stripe', [
                'error' => $e->getMessage(),
            ]);

            return [
                'exitoso' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function reembolsar($orden): array
    {
        try {
            // Implementación futura con SDK de Stripe
            // $stripe = new \Stripe\StripeClient($this->secretKey);
            // $refund = $stripe->refunds->create([
            //     'payment_intent' => $orden->payment_intent_id,
            // ]);

            Log::info('Procesando reembolso con Stripe', [
                'orden_id' => $orden->id,
            ]);

            return [
                'exitoso' => true,
                'refund_id' => 're_' . uniqid(),
                'mensaje' => 'Reembolso procesado exitosamente',
            ];

        } catch (\Exception $e) {
            Log::error('Error al procesar reembolso con Stripe', [
                'error' => $e->getMessage(),
            ]);

            return [
                'exitoso' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }
}
