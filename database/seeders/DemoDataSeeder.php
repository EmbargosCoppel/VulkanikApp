<?php

namespace Database\Seeders;

use App\Models\Cliente;
use App\Models\OrdenTrabajo;
use App\Models\User;
use App\Models\Vehiculo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DemoDataSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('es_MX');

        // Create users until we have at least 5 (keeps existing AdminSeeder values)
        $existingUsers = User::count();
        $targetUsers = 5;
        for ($i = $existingUsers; $i < $targetUsers; $i++) {
            $role = $i % 2 === 0 ? 'mecanico' : 'user';
            User::firstOrCreate([
                'email' => $faker->unique()->safeEmail(),
            ], [
                'name' => $faker->name(),
                'password' => Hash::make('password'),
                'role' => $role,
                'email_verified_at' => now(),
            ]);
        }

        // Create clientes until at least 4
        $existingClientes = Cliente::count();
        $targetClientes = 4;
        for ($i = $existingClientes; $i < $targetClientes; $i++) {
            Cliente::firstOrCreate([
                'email' => $faker->unique()->safeEmail(),
            ], [
                'nombre' => $faker->name(),
                'telefono' => $faker->phoneNumber(),
                'direccion' => $faker->address(),
                'rfc' => strtoupper($faker->bothify('???######???')),
                'es_empresa' => false,
            ]);
        }

        // Create vehiculos until at least 5
        $existingVehiculos = Vehiculo::count();
        $targetVehiculos = 5;
        $clientes = Cliente::all();
        for ($i = $existingVehiculos; $i < $targetVehiculos; $i++) {
            $cliente = $clientes->random();
            Vehiculo::firstOrCreate([
                'placa' => strtoupper($faker->bothify('AAA-###')),
            ], [
                'cliente_id' => $cliente->id,
                'marca' => $faker->company(),
                'modelo' => $faker->word(),
                'anio' => $faker->numberBetween(2000, 2023),
                'color' => $faker->safeColorName(),
                'vin' => strtoupper($faker->bothify('1???????????????')),
            ]);
        }

        // Create 4 ordenes de trabajo with realistic values
        $existingOrdenes = OrdenTrabajo::count();
        $targetOrdenes = 4;
        $vehiculos = Vehiculo::all();
        $mecanicos = User::where('role', 'mecanico')->get();
        $estados = ['diagnóstico', 'esperando_piezas', 'reparación', 'finalizado'];

        for ($i = $existingOrdenes; $i < $targetOrdenes; $i++) {
            if ($vehiculos->isEmpty() || $mecanicos->isEmpty()) {
                break;
            }

            $vehiculo = $vehiculos->random();
            $mecanico = $mecanicos->random();

            OrdenTrabajo::create([
                'vehiculo_id' => $vehiculo->id,
                'mecanico_id' => $mecanico->id,
                'estado' => $faker->randomElement($estados),
                'diagnostico' => $faker->sentence(6),
                'trabajos_realizados' => $faker->sentence(6),
                'mano_obra' => $faker->randomFloat(2, 100, 1500),
                'subtotal' => $faker->randomFloat(2, 200, 5000),
                'iva' => 0,
                'total' => $faker->randomFloat(2, 300, 6500),
                'fecha_entrada' => now()->subDays($faker->numberBetween(1, 30)),
                'observaciones' => $faker->optional()->sentence(),
            ]);
        }
    }
}
