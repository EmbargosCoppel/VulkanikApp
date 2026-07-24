<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehiculoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_puede_ver_lista_de_vehiculos(): void
    {
        Vehiculo::factory()->count(3)->create();

        $response = $this->get(route('vehiculos.index'));

        $response->assertStatus(200);
        $response->assertViewHas('vehiculos');
    }

    public function test_puede_crear_vehiculo(): void
    {
        $cliente = Cliente::factory()->create();

        $vehiculoData = [
            'cliente_id' => $cliente->id,
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2020,
            'placa' => 'ABC123',
            'color' => 'Rojo',
            'vin' => '1HGCM82633A123456',
            'notas' => 'Vehículo en buen estado',
        ];

        $response = $this->post(route('vehiculos.store'), $vehiculoData);

        $response->assertRedirect(route('vehiculos.index'));
        $this->assertDatabaseHas('vehiculos', [
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'placa' => 'ABC123',
        ]);
    }

    public function test_validacion_cliente_id_requerido(): void
    {
        $response = $this->post(route('vehiculos.store'), [
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2020,
            'placa' => 'ABC123',
        ]);

        $response->assertSessionHasErrors('cliente_id');
    }

    public function test_validacion_marca_requerida(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->post(route('vehiculos.store'), [
            'cliente_id' => $cliente->id,
            'marca' => '',
            'modelo' => 'Corolla',
            'anio' => 2020,
            'placa' => 'ABC123',
        ]);

        $response->assertSessionHasErrors('marca');
    }

    public function test_validacion_placa_unica(): void
    {
        $cliente = Cliente::factory()->create();
        Vehiculo::factory()->create(['placa' => 'ABC123']);

        $response = $this->post(route('vehiculos.store'), [
            'cliente_id' => $cliente->id,
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2020,
            'placa' => 'ABC123',
        ]);

        $response->assertSessionHasErrors('placa');
    }

    public function test_validacion_anio_minimo(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->post(route('vehiculos.store'), [
            'cliente_id' => $cliente->id,
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 1800,
            'placa' => 'ABC123',
        ]);

        $response->assertSessionHasErrors('anio');
    }

    public function test_puede_ver_detalle_vehiculo(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $response = $this->get(route('vehiculos.show', $vehiculo));

        $response->assertStatus(200);
        $response->assertViewHas('vehiculo');
    }

    public function test_puede_editar_vehiculo(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $response = $this->get(route('vehiculos.edit', $vehiculo));

        $response->assertStatus(200);
        $response->assertViewHas('vehiculo');
        $response->assertViewHas('clientes');
    }

    public function test_puede_actualizar_vehiculo(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $response = $this->put(route('vehiculos.update', $vehiculo), [
            'cliente_id' => $vehiculo->cliente_id,
            'marca' => 'Honda',
            'modelo' => 'Civic',
            'anio' => 2021,
            'placa' => 'XYZ789',
        ]);

        $response->assertRedirect(route('vehiculos.index'));
        $this->assertDatabaseHas('vehiculos', [
            'id' => $vehiculo->id,
            'marca' => 'Honda',
            'modelo' => 'Civic',
        ]);
    }

    public function test_puede_eliminar_vehiculo(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $response = $this->delete(route('vehiculos.destroy', $vehiculo));

        $response->assertRedirect(route('vehiculos.index'));
        $this->assertDatabaseMissing('vehiculos', [
            'id' => $vehiculo->id,
        ]);
    }

    public function test_vehiculo_carga_relacion_cliente(): void
    {
        $vehiculo = Vehiculo::factory()->create();

        $response = $this->get(route('vehiculos.index'));

        $response->assertStatus(200);
        $this->assertNotNull($vehiculo->cliente);
    }
}
