<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrdenTrabajo>
 */
class OrdenTrabajoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehiculo_id' => Vehiculo::factory(),
            'mecanico_id' => User::factory(),
            'estado' => fake()->randomElement(['diagnóstico', 'esperando_piezas', 'reparación', 'finalizado']),
            'diagnostico' => fake()->optional()->sentence(),
            'trabajos_realizados' => fake()->optional()->paragraph(),
            'mano_obra' => fake()->randomFloat(2, 0, 1000),
            'subtotal' => fake()->randomFloat(2, 0, 5000),
            'iva' => fake()->randomFloat(2, 0, 800),
            'total' => fake()->randomFloat(2, 0, 5800),
            'fecha_entrada' => fake()->dateTimeBetween('-1 month', 'now'),
            'fecha_salida' => fake()->optional()->dateTimeBetween('now', '+1 month'),
            'observaciones' => fake()->optional()->sentence(),
        ];
    }
}
