<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cliente>
 */
class ClienteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->optional()->email(),
            'direccion' => fake()->optional()->address(),
            'rfc' => fake()->optional()->regexify('[A-Z]{4}[0-9]{6}[A-Z0-9]{3}'),
            'es_empresa' => fake()->boolean(20), // 20% probabilidad de ser empresa
            'nombre_empresa' => fake()->optional()->company(),
        ];
    }
}
