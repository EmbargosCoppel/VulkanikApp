<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MecanicoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs(User::factory()->create(['role' => 'admin']));
    }

    public function test_puede_ver_lista_de_mecanicos(): void
    {
        User::factory()->count(3)->create(['role' => 'mecanico']);

        $response = $this->get(route('mecanicos.index'));

        $response->assertStatus(200);
        $response->assertViewHas('mecanicos');
    }

    public function test_puede_crear_mecanico(): void
    {
        $response = $this->post(route('mecanicos.store'), [
            'name' => 'Juan Mecánico',
            'email' => 'juan@taller.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('mecanicos.index'));
        $this->assertDatabaseHas('users', [
            'name' => 'Juan Mecánico',
            'email' => 'juan@taller.com',
            'role' => 'mecanico',
        ]);
    }

    public function test_validacion_email_unico(): void
    {
        User::factory()->create(['email' => 'juan@taller.com', 'role' => 'mecanico']);

        $response = $this->post(route('mecanicos.store'), [
            'name' => 'Otro Mecánico',
            'email' => 'juan@taller.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_puede_ver_detalle_mecanico(): void
    {
        $mecanico = User::factory()->create(['role' => 'mecanico']);

        $response = $this->get(route('mecanicos.show', $mecanico));

        $response->assertStatus(200);
        $response->assertViewHas('mecanico');
    }

    public function test_puede_editar_mecanico(): void
    {
        $mecanico = User::factory()->create(['role' => 'mecanico']);

        $response = $this->get(route('mecanicos.edit', $mecanico));

        $response->assertStatus(200);
        $response->assertViewHas('mecanico');
    }

    public function test_puede_actualizar_mecanico(): void
    {
        $mecanico = User::factory()->create(['role' => 'mecanico']);

        $response = $this->put(route('mecanicos.update', $mecanico), [
            'name' => 'Juan Actualizado',
            'email' => 'juan.nuevo@taller.com',
        ]);

        $response->assertRedirect(route('mecanicos.index'));
        $this->assertDatabaseHas('users', [
            'id' => $mecanico->id,
            'name' => 'Juan Actualizado',
        ]);
    }

    public function test_puede_eliminar_mecanico(): void
    {
        $mecanico = User::factory()->create(['role' => 'mecanico']);

        $response = $this->delete(route('mecanicos.destroy', $mecanico));

        $response->assertRedirect(route('mecanicos.index'));
        $this->assertSoftDeleted('users', [
            'id' => $mecanico->id,
        ]);
    }

    public function test_no_puede_eliminar_si_mismo(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $response = $this->delete(route('mecanicos.destroy', $admin));

        $response->assertRedirect(route('mecanicos.index'));
        $response->assertSessionHas('error');
    }
}