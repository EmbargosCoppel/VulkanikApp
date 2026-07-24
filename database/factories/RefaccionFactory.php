<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Refaccion>
 */
class RefaccionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->randomElement([
                'Filtro de aceite', 'Pastillas de freno', 'Bujías', 'Batería',
                'Amortiguadores', 'Neumático', 'Correa de distribución',
                'Alternador', 'Motor de arranque', 'Radiador'
            ]),
            'sku' => fake()->unique()->regexify('REF-[0-9]{6}'),
            'descripcion' => fake()->sentence(),
            'costo' => fake()->randomFloat(2, 50, 500),
            'precio_venta' => fake()->randomFloat(2, 100, 1000),
            'stock_actual' => fake()->numberBetween(0, 50),
            'stock_minimo' => fake()->numberBetween(5, 15),
            'ubicacion' => fake()->randomElement(['Pasillo A', 'Pasillo B', 'Pasillo C']),
            'proveedor' => fake()->company(),
            'activo' => true,
        ];
    }
}
