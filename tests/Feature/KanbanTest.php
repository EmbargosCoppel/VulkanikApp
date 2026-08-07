<?php

namespace Tests\Feature;

use App\Models\OrdenTrabajo;
use App\Models\User;
use App\Models\Vehiculo;
use App\Models\Cliente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KanbanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_puede_ver_tablero_kanban(): void
    {
        $cliente = Cliente::factory()->create();
        $vehiculo = Vehiculo::factory()->create(['cliente_id' => $cliente->id]);
        OrdenTrabajo::factory()->count(3)->create(['vehiculo_id' => $vehiculo->id]);

        $response = $this->get(route('ordenes.kanban'));

        // Debug: ver qué está pasando
        if ($response->getStatusCode() !== 200) {
            echo "Status: " . $response->getStatusCode() . "\n";
            echo "Content: " . $response->getContent() . "\n";
        }
        
        $response->assertStatus(200);
        $response->assertViewHas('ordenes');
    }

    public function test_kanban_agrupa_ordenes_por_estado(): void
    {
        $cliente = Cliente::factory()->create();
        $vehiculo = Vehiculo::factory()->create(['cliente_id' => $cliente->id]);
        
        OrdenTrabajo::factory()->create(['vehiculo_id' => $vehiculo->id, 'estado' => 'diagnóstico']);
        OrdenTrabajo::factory()->create(['vehiculo_id' => $vehiculo->id, 'estado' => 'reparación']);
        OrdenTrabajo::factory()->create(['vehiculo_id' => $vehiculo->id, 'estado' => 'finalizado']);

        $response = $this->get(route('ordenes.kanban'));

        $response->assertStatus(200);
        $ordenes = $response->viewData('ordenes');
        
        $this->assertArrayHasKey('diagnóstico', $ordenes);
        $this->assertArrayHasKey('reparación', $ordenes);
        $this->assertArrayHasKey('finalizado', $ordenes);
    }
}