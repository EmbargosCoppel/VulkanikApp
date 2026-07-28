<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_solicitar_link_de_restablecimiento_se_muestra(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_link_de_restablecimiento_se_puede_solicitar(): void
    {
        Notification::fake();

        $user = User::factory()->create(['email' => 'test@example.com']);

        $response = $this->post('/forgot-password', [
            'email' => 'test@example.com',
        ]);

        Notification::assertSentTo($user, ResetPassword::class);
        $response->assertSessionHas('status', __('Se ha enviado el enlace de restablecimiento de contraseña.'));
    }

    public function test_no_se_puede_solicitar_link_para_email_inexistente(): void
    {
        Notification::fake();

        $response = $this->post('/forgot-password', [
            'email' => 'nonexistent@example.com',
        ]);

        Notification::assertNothingSent();
        $response->assertSessionHasErrors('email');
    }

    public function test_validacion_email_requerido_al_solicitar_restablecimiento(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => '',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_validacion_email_formato_invalido_al_solicitar_restablecimiento(): void
    {
        $response = $this->post('/forgot-password', [
            'email' => 'not-an-email',
        ]);

        $response->assertSessionHasErrors('email');
    }
}
