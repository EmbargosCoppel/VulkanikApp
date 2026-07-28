<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClienteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_puede_ver_lista_de_clientes(): void
    {
        Cliente::factory()->count(3)->create();

        $response = $this->get(route('clientes.index'));

        $response->assertStatus(200);
        $response->assertViewHas('clientes');
    }

    public function test_puede_crear_cliente(): void
    {
        $clienteData = [
            'nombre' => 'Juan Pérez',
            'telefono' => '555-1234',
            'email' => 'juan@example.com',
            'direccion' => 'Calle 123',
            'rfc' => 'ABC123456XYZ',
            'es_empresa' => false,
        ];

        $response = $this->post(route('clientes.store'), $clienteData);

        $response->assertRedirect(route('clientes.index'));
        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Juan Pérez',
            'email' => 'juan@example.com',
        ]);
    }

    public function test_validacion_nombre_requerido(): void
    {
        $response = $this->post(route('clientes.store'), [
            'nombre' => '',
            'telefono' => '555-1234',
        ]);

        $response->assertSessionHasErrors('nombre');
    }

    public function test_validacion_telefono_requerido(): void
    {
        $response = $this->post(route('clientes.store'), [
            'nombre' => 'Juan Pérez',
            'telefono' => '',
        ]);

        $response->assertSessionHasErrors('telefono');
    }

    public function test_puede_ver_detalle_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->get(route('clientes.show', $cliente));

        $response->assertStatus(200);
        $response->assertViewHas('cliente');
    }

    public function test_puede_editar_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->get(route('clientes.edit', $cliente));

        $response->assertStatus(200);
        $response->assertViewHas('cliente');
    }

    public function test_puede_actualizar_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->put(route('clientes.update', $cliente), [
            'nombre' => 'Juan Pérez Actualizado',
            'telefono' => '555-9999',
            'email' => 'juan.nuevo@example.com',
        ]);

        $response->assertRedirect(route('clientes.index'));
        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nombre' => 'Juan Pérez Actualizado',
        ]);
    }

    public function test_puede_eliminar_cliente(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->delete(route('clientes.destroy', $cliente));

        $response->assertRedirect(route('clientes.index'));
        $this->assertSoftDeleted('clientes', [
            'id' => $cliente->id,
        ]);
    }

    public function test_puede_crear_cliente_empresa(): void
    {
        $clienteData = [
            'nombre' => 'Empresa SA',
            'telefono' => '555-5678',
            'email' => 'contacto@empresa.com',
            'es_empresa' => true,
            'nombre_empresa' => 'Empresa SA de CV',
        ];

        $response = $this->post(route('clientes.store'), $clienteData);

        $response->assertRedirect(route('clientes.index'));
        $this->assertDatabaseHas('clientes', [
            'nombre' => 'Empresa SA',
            'es_empresa' => true,
            'nombre_empresa' => 'Empresa SA de CV',
        ]);
    }

    public function test_busqueda_filtra_por_nombre(): void
    {
        Cliente::factory()->create(['nombre' => 'Juan Pérez']);
        Cliente::factory()->create(['nombre' => 'María López']);

        $response = $this->get(route('clientes.index', ['search' => 'Juan']));

        $response->assertStatus(200);
        $clientes = $response->viewData('clientes');
        $this->assertCount(1, $clientes);
        $this->assertEquals('Juan Pérez', $clientes->first()->nombre);
    }

    public function test_busqueda_filtra_por_email(): void
    {
        Cliente::factory()->create(['email' => 'juan@example.com']);
        Cliente::factory()->create(['email' => 'maria@example.com']);

        $response = $this->get(route('clientes.index', ['search' => 'juan']));

        $response->assertStatus(200);
        $clientes = $response->viewData('clientes');
        $this->assertCount(1, $clientes);
    }

    public function test_lista_usa_paginacion(): void
    {
        Cliente::factory()->count(20)->create();

        $response = $this->get(route('clientes.index'));

        $response->assertStatus(200);
        $clientes = $response->viewData('clientes');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $clientes);
        $this->assertEquals(15, $clientes->perPage());
    }
}
