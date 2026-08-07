<?php

namespace Tests\Feature;

use App\Models\OrdenTrabajo;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_puede_ver_ticket_de_orden(): void
    {
        $cliente = Cliente::factory()->create();
        $vehiculo = Vehiculo::factory()->create(['cliente_id' => $cliente->id]);
        $orden = OrdenTrabajo::factory()->create(['vehiculo_id' => $vehiculo->id]);

        $response = $this->get(route('ordenes.ticket', $orden));

        $response->assertStatus(200);
        $response->assertViewHas('ordenTrabajo');
        $response->assertViewHas('totales');
    }

    public function test_ticket_muestra_datos_correctos(): void
    {
        $cliente = Cliente::factory()->create(['nombre' => 'Cliente Test']);
        $vehiculo = Vehiculo::factory()->create(['cliente_id' => $cliente->id, 'marca' => 'Toyota', 'modelo' => 'Corolla']);
        $orden = OrdenTrabajo::factory()->create(['vehiculo_id' => $vehiculo->id]);

        $response = $this->get(route('ordenes.ticket', $orden));

        $response->assertStatus(200);
        $response->assertSee('VULCANIZADORA DON CHUY');
        $response->assertSee('#'.$orden->id);
        $response->assertSee('Cliente Test');
        $response->assertSee('Toyota');
    }
}