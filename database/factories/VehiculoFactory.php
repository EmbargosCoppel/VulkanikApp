<?php

namespace Database\Factories;

use App\Models\Cliente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehiculo>
 */
class VehiculoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'cliente_id' => Cliente::factory(),
            'marca' => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Nissan', 'Volkswagen']),
            'modelo' => fake()->randomElement(['Corolla', 'Civic', 'Fiesta', 'Aveo', 'Sentra', 'Jetta']),
            'anio' => fake()->numberBetween(2010, 2024),
            'placa' => fake()->unique()->regexify('[A-Z]{3}[0-9]{3}'),
            'color' => fake()->optional()->colorName(),
            'vin' => fake()->optional()->regexify('[A-Z0-9]{17}'),
            'notas' => fake()->optional()->sentence(),
        ];
    }
}
