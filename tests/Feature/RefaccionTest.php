<?php

namespace Tests\Feature;

use App\Models\Refaccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RefaccionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_puede_ver_lista_de_refacciones(): void
    {
        Refaccion::factory()->count(5)->create();

        $response = $this->get(route('refacciones.index'));

        $response->assertStatus(200);
        $response->assertViewHas('refacciones');
    }

    public function test_puede_crear_refaccion(): void
    {
        $refaccionData = [
            'nombre' => 'Filtro de aceite',
            'sku' => 'REF-001',
            'descripcion' => 'Filtro de aceite premium',
            'costo' => 50.00,
            'precio_venta' => 100.00,
            'stock_actual' => 20,
            'stock_minimo' => 5,
            'ubicacion' => 'Pasillo A',
            'proveedor' => 'AutoParts SA',
        ];

        $response = $this->post(route('refacciones.store'), $refaccionData);

        $response->assertRedirect(route('refacciones.index'));
        $this->assertDatabaseHas('refacciones', [
            'nombre' => 'Filtro de aceite',
            'sku' => 'REF-001',
        ]);
    }

    public function test_validacion_nombre_requerido(): void
    {
        $response = $this->post(route('refacciones.store'), [
            'nombre' => '',
            'sku' => 'REF-001',
            'costo' => 50.00,
            'precio_venta' => 100.00,
            'stock_actual' => 20,
            'stock_minimo' => 5,
        ]);

        $response->assertSessionHasErrors('nombre');
    }

    public function test_validacion_sku_requerido(): void
    {
        $response = $this->post(route('refacciones.store'), [
            'nombre' => 'Filtro de aceite',
            'sku' => '',
            'costo' => 50.00,
            'precio_venta' => 100.00,
            'stock_actual' => 20,
            'stock_minimo' => 5,
        ]);

        $response->assertSessionHasErrors('sku');
    }

    public function test_validacion_sku_unico(): void
    {
        Refaccion::factory()->create(['sku' => 'REF-001']);

        $response = $this->post(route('refacciones.store'), [
            'nombre' => 'Filtro de aire',
            'sku' => 'REF-001',
            'costo' => 30.00,
            'precio_venta' => 60.00,
            'stock_actual' => 15,
            'stock_minimo' => 3,
        ]);

        $response->assertSessionHasErrors('sku');
    }

    public function test_validacion_costo_minimo(): void
    {
        $response = $this->post(route('refacciones.store'), [
            'nombre' => 'Filtro de aceite',
            'sku' => 'REF-002',
            'costo' => -10.00,
            'precio_venta' => 100.00,
            'stock_actual' => 20,
            'stock_minimo' => 5,
        ]);

        $response->assertSessionHasErrors('costo');
    }

    public function test_puede_ver_detalle_refaccion(): void
    {
        $refaccion = Refaccion::factory()->create();

        $response = $this->get(route('refacciones.show', $refaccion));

        $response->assertStatus(200);
        $response->assertViewHas('refaccion');
    }

    public function test_puede_editar_refaccion(): void
    {
        $refaccion = Refaccion::factory()->create();

        $response = $this->get(route('refacciones.edit', $refaccion));

        $response->assertStatus(200);
        $response->assertViewHas('refaccion');
    }

    public function test_puede_actualizar_refaccion(): void
    {
        $refaccion = Refaccion::factory()->create();

        $response = $this->put(route('refacciones.update', $refaccion), [
            'nombre' => 'Filtro de aceite actualizado',
            'sku' => 'REF-003',
            'costo' => 55.00,
            'precio_venta' => 110.00,
            'stock_actual' => 25,
            'stock_minimo' => 10,
            'activo' => true,
        ]);

        $response->assertRedirect(route('refacciones.index'));
        $this->assertDatabaseHas('refacciones', [
            'id' => $refaccion->id,
            'nombre' => 'Filtro de aceite actualizado',
            'sku' => 'REF-003',
        ]);
    }

    public function test_puede_eliminar_refaccion(): void
    {
        $refaccion = Refaccion::factory()->create();

        $response = $this->delete(route('refacciones.destroy', $refaccion));

        $response->assertRedirect(route('refacciones.index'));
        $this->assertSoftDeleted('refacciones', [
            'id' => $refaccion->id,
        ]);
    }

    public function test_puede_ver_stock_bajo(): void
    {
        Refaccion::factory()->create(['stock_actual' => 10, 'stock_minimo' => 5]);
        Refaccion::factory()->create(['stock_actual' => 3, 'stock_minimo' => 5]);
        Refaccion::factory()->create(['stock_actual' => 2, 'stock_minimo' => 10]);

        $response = $this->get(route('refacciones.stock-bajo'));

        $response->assertStatus(200);
        $response->assertViewHas('refacciones');
    }

    public function test_puede_ver_pagina_actualizar_stock(): void
    {
        $refaccion = Refaccion::factory()->create();

        $response = $this->get(route('refacciones.stock', $refaccion));

        $response->assertStatus(200);
        $response->assertViewHas('refaccion');
    }

    public function test_puede_actualizar_stock(): void
    {
        $refaccion = Refaccion::factory()->create(['stock_actual' => 10]);

        $response = $this->put(route('refacciones.actualizarStock', $refaccion), [
            'nuevo_stock' => 25,
        ]);

        $response->assertRedirect(route('refacciones.index'));
        $this->assertDatabaseHas('refacciones', [
            'id' => $refaccion->id,
            'stock_actual' => 25,
        ]);
    }

    public function test_validacion_stock_no_negativo(): void
    {
        $refaccion = Refaccion::factory()->create(['stock_actual' => 10]);

        $response = $this->put(route('refacciones.actualizarStock', $refaccion), [
            'nuevo_stock' => -5,
        ]);

        $response->assertSessionHasErrors('nuevo_stock');
    }

    public function test_metodo_esta_bajo_stock(): void
    {
        $refaccionBajoStock = Refaccion::factory()->create([
            'stock_actual' => 3,
            'stock_minimo' => 5
        ]);

        $refaccionNormal = Refaccion::factory()->create([
            'stock_actual' => 10,
            'stock_minimo' => 5
        ]);

        $this->assertTrue($refaccionBajoStock->estaBajoStock());
        $this->assertFalse($refaccionNormal->estaBajoStock());
    }

    public function test_solo_muestra_refacciones_activas(): void
    {
        Refaccion::factory()->create(['activo' => true]);
        Refaccion::factory()->create(['activo' => true]);
        Refaccion::factory()->create(['activo' => false]);

        $response = $this->get(route('refacciones.index'));

        $response->assertStatus(200);
        $refacciones = $response->viewData('refacciones');
        $this->assertCount(2, $refacciones);
        foreach ($refacciones as $refaccion) {
            $this->assertTrue($refaccion->activo);
        }
    }

    public function test_lista_usa_paginacion(): void
    {
        Refaccion::factory()->count(20)->create();

        $response = $this->get(route('refacciones.index'));

        $response->assertStatus(200);
        $refacciones = $response->viewData('refacciones');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $refacciones);
        $this->assertEquals(15, $refacciones->perPage());
    }

    public function test_busqueda_filtra_por_nombre(): void
    {
        Refaccion::factory()->create(['nombre' => 'Filtro de aceite']);
        Refaccion::factory()->create(['nombre' => 'Filtro de aire']);

        $response = $this->get(route('refacciones.index', ['search' => 'aceite']));

        $response->assertStatus(200);
        $refacciones = $response->viewData('refacciones');
        $this->assertCount(1, $refacciones);
        $this->assertEquals('Filtro de aceite', $refacciones->first()->nombre);
    }

    public function test_busqueda_filtra_por_sku(): void
    {
        Refaccion::factory()->create(['sku' => 'REF-001']);
        Refaccion::factory()->create(['sku' => 'REF-002']);

        $response = $this->get(route('refacciones.index', ['search' => 'REF-001']));

        $response->assertStatus(200);
        $refacciones = $response->viewData('refacciones');
        $this->assertCount(1, $refacciones);
    }
}
