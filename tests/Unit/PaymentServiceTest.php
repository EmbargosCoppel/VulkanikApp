<?php

namespace Tests\Unit;

use App\Models\OrdenTrabajo;
use App\Models\Pago;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Cliente;
use App\Services\PaymentService;
use App\Services\Adapters\StripePaymentAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentService $paymentService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentService = new PaymentService(new StripePaymentAdapter());
    }

    public function test_no_puede_procesar_pago_orden_finalizada(): void
    {
        $cliente = Cliente::factory()->create();
        $vehiculo = Vehiculo::factory()->create(['cliente_id' => $cliente->id]);
        $orden = OrdenTrabajo::factory()->create(['vehiculo_id' => $vehiculo->id, 'estado' => 'finalizado']);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('La orden ya está finalizada y pagada');

        $this->paymentService->procesarPago($orden, [
            'payment_method_id' => 'pm_test_123',
        ]);
    }

    public function test_pago_guardado_en_base_de_datos(): void
    {
        $cliente = Cliente::factory()->create();
        $vehiculo = Vehiculo::factory()->create(['cliente_id' => $cliente->id]);
        $orden = OrdenTrabajo::factory()->create(['vehiculo_id' => $vehiculo->id, 'estado' => 'reparación']);

        // Mock del adapter para evitar llamada real a Stripe
        $mockAdapter = new class implements \App\Services\PaymentAdapterInterface {
            public function procesarPago(float $monto, array $datosPago): array
            {
                return [
                    'exitoso' => true,
                    'transaction_id' => 'pi_test_123',
                    'mensaje' => 'Pago exitoso',
                    'status' => 'succeeded',
                ];
            }
            public function reembolsar($orden): array
            {
                return ['exitoso' => true];
            }
        };

        $service = new PaymentService($mockAdapter);
        $resultado = $service->procesarPago($orden, ['payment_method_id' => 'pm_test_123']);

        $this->assertTrue($resultado['exitoso']);
        $this->assertDatabaseHas('pagos', [
            'orden_trabajo_id' => $orden->id,
            'transaction_id' => 'pi_test_123',
            'estado' => 'completado',
        ]);
        
        $orden->refresh();
        $this->assertEquals('finalizado', $orden->estado);
    }
}