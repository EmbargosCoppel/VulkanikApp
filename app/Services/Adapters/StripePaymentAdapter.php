<?php

namespace App\Services\Adapters;

use App\Services\PaymentAdapterInterface;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class StripePaymentAdapter implements PaymentAdapterInterface
{
    private ?string $secretKey;
    private ?string $publicKey;
    private ?StripeClient $stripe = null;

    public function __construct()
    {
        $this->secretKey = config('services.stripe.secret');
        $this->publicKey = config('services.stripe.key');

        if ($this->secretKey) {
            $this->stripe = new StripeClient($this->secretKey);
        }
    }

    public function procesarPago(float $monto, array $datosPago): array
    {
        try {
            if (!$this->stripe) {
                throw new \RuntimeException('Stripe no está configurado. Verifica STRIPE_SECRET en .env');
            }

            $paymentIntent = $this->stripe->paymentIntents->create([
                'amount' => (int) round($monto * 100), // Stripe usa centavos
                'currency' => 'mxn',
                'payment_method' => $datosPago['payment_method_id'],
                'confirm' => true,
                'automatic_payment_methods' => [
                    'enabled' => true,
                    'allow_redirects' => 'never',
                ],
                'metadata' => [
                    'orden_id' => $datosPago['orden_id'] ?? null,
                ],
            ]);

            return [
                'exitoso' => $paymentIntent->status === 'succeeded',
                'transaction_id' => $paymentIntent->id,
                'mensaje' => $paymentIntent->status === 'succeeded'
                    ? 'Pago procesado exitosamente'
                    : 'Pago pendiente de confirmación',
                'status' => $paymentIntent->status,
            ];

        } catch (\Exception $e) {
            Log::error('Error al procesar pago con Stripe', [
                'error' => $e->getMessage(),
                'monto' => $monto,
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
            if (!$this->stripe) {
                throw new \RuntimeException('Stripe no está configurado. Verifica STRIPE_SECRET en .env');
            }

            // Buscar el payment intent asociado a la orden
            $pago = \App\Models\Pago::where('orden_trabajo_id', $orden->id)
                ->where('estado', 'completado')
                ->latest()
                ->first();

            if (!$pago) {
                throw new \RuntimeException('No se encontró un pago para esta orden');
            }

            $refund = $this->stripe->refunds->create([
                'payment_intent' => $pago->transaction_id,
            ]);

            // Actualizar el estado del pago
            $pago->estado = 'reembolsado';
            $pago->save();

            return [
                'exitoso' => $refund->status === 'succeeded',
                'refund_id' => $refund->id,
                'mensaje' => 'Reembolso procesado exitosamente',
            ];

        } catch (\Exception $e) {
            Log::error('Error al procesar reembolso con Stripe', [
                'error' => $e->getMessage(),
                'orden_id' => $orden->id,
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

    public function generarLinkPago(float $monto, array $datosPago): array
    {
        try {
            if (!$this->stripe) {
                throw new \RuntimeException('Stripe no está configurado. Verifica STRIPE_SECRET en .env');
            }

            // Crear un producto para el pago
            $product = $this->stripe->products->create([
                'name' => $datosPago['descripcion'] ?? 'Pago de Orden de Trabajo',
            ]);

            // Crear el precio del producto
            $price = $this->stripe->prices->create([
                'product' => $product->id,
                'unit_amount' => (int) round($monto * 100), // Stripe usa centavos
                'currency' => 'mxn',
            ]);

            // Crear el Payment Link
            $paymentLink = $this->stripe->paymentLinks->create([
                'line_items' => [
                    [
                        'price' => $price->id,
                        'quantity' => 1,
                    ],
                ],
                'after_completion' => [
                    'type' => 'redirect',
                    'redirect' => [
                        'url' => $datosPago['url_retorno'] ?? config('app.url'),
                    ],
                ],
                'metadata' => [
                    'orden_id' => $datosPago['orden_id'] ?? null,
                    'cliente_email' => $datosPago['cliente_email'] ?? null,
                ],
            ]);

            return [
                'exitoso' => true,
                'payment_link_url' => $paymentLink->url,
                'payment_link_id' => $paymentLink->id,
                'mensaje' => 'Link de pago generado exitosamente',
            ];

        } catch (\Exception $e) {
            Log::error('Error al generar link de pago con Stripe', [
                'error' => $e->getMessage(),
                'monto' => $monto,
            ]);

            return [
                'exitoso' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
