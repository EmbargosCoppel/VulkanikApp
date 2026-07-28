<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\OrdenTrabajo;
use App\Models\Refaccion;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_admin_se_muestra_correctamente(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Cliente::factory()->count(5)->create();
        Vehiculo::factory()->count(10)->create();
        OrdenTrabajo::factory()->count(3)->create();
        Refaccion::factory()->count(8)->create();

        $response = $this->actingAs($admin)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertViewHas('ordenes_recientes');
        $response->assertViewHas('refacciones_stock_bajo');
    }

    public function test_dashboard_mecanico_se_muestra_correctamente(): void
    {
        $mecanico = User::factory()->create(['role' => 'mecanico']);

        $response = $this->actingAs($mecanico)->get('/');

        $response->assertStatus(200);
        $response->assertViewHas('ordenes_asignadas');
        $response->assertViewHas('ordenes_completadas');
        $response->assertViewHas('ordenes_pendientes');
    }

    public function test_stats_calculados_correctamente_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Cliente::factory()->count(5)->create();
        Vehiculo::factory()->count(10)->create();
        OrdenTrabajo::factory()->count(3)->create();
        Refaccion::factory()->count(8)->create();

        $response = $this->actingAs($admin)->get('/');

        $stats = $response->viewData('stats');

        $this->assertEquals(Cliente::count(), $stats['clientes']);
        $this->assertEquals(Vehiculo::count(), $stats['vehiculos']);
        $this->assertEquals(OrdenTrabajo::count(), $stats['ordenes']);
        $this->assertEquals(Refaccion::where('activo', true)->count(), $stats['refacciones']);
    }

    public function test_ordenes_pendientes_incluyen_estados_correctos(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        OrdenTrabajo::factory()->create(['estado' => 'diagnóstico']);
        OrdenTrabajo::factory()->create(['estado' => 'esperando_piezas']);
        OrdenTrabajo::factory()->create(['estado' => 'reparación']);
        OrdenTrabajo::factory()->create(['estado' => 'finalizado']);

        $response = $this->actingAs($admin)->get('/');

        $stats = $response->viewData('stats');

        $this->assertEquals(3, $stats['ordenes_pendientes']);
        $this->assertEquals(1, $stats['ordenes_finalizadas']);
    }

    public function test_stock_bajo_calculado_correctamente(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        Refaccion::factory()->create(['stock_actual' => 10, 'stock_minimo' => 5, 'activo' => true]);
        Refaccion::factory()->create(['stock_actual' => 3, 'stock_minimo' => 5, 'activo' => true]);
        Refaccion::factory()->create(['stock_actual' => 2, 'stock_minimo' => 10, 'activo' => true]);
        Refaccion::factory()->create(['stock_actual' => 1, 'stock_minimo' => 5, 'activo' => false]);

        $response = $this->actingAs($admin)->get('/');

        $stats = $response->viewData('stats');

        $this->assertEquals(2, $stats['stock_bajo']);
    }

    public function test_mecanico_solo_ve_sus_ordenes(): void
    {
        $mecanico1 = User::factory()->create(['role' => 'mecanico']);
        $mecanico2 = User::factory()->create(['role' => 'mecanico']);

        $vehiculo = Vehiculo::factory()->create();

        OrdenTrabajo::factory()->create([
            'mecanico_id' => $mecanico1->id,
            'vehiculo_id' => $vehiculo->id,
            'estado' => 'diagnóstico',
        ]);

        OrdenTrabajo::factory()->create([
            'mecanico_id' => $mecanico2->id,
            'vehiculo_id' => $vehiculo->id,
            'estado' => 'reparación',
        ]);

        $response = $this->actingAs($mecanico1)->get('/');

        $ordenesAsignadas = $response->viewData('ordenes_asignadas');

        $this->assertCount(1, $ordenesAsignadas);
        $this->assertEquals($mecanico1->id, $ordenesAsignadas->first()->mecanico_id);
    }
}
