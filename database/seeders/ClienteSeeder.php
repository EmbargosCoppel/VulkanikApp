<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cliente1 = \App\Models\Cliente::firstOrCreate(
            ['email' => 'carlos@email.com'],
            [
                'nombre' => 'Carlos López',
                'telefono' => '555-1234',
                'direccion' => 'Av. Principal 123',
                'rfc' => 'LOCA800101',
                'es_empresa' => false,
            ]
        );

        $cliente2 = \App\Models\Cliente::firstOrCreate(
            ['email' => 'contacto@transportes.com'],
            [
                'nombre' => 'Transportes Rápidos SA',
                'telefono' => '555-5678',
                'direccion' => 'Industrial Zone 456',
                'rfc' => 'TRA900101',
                'es_empresa' => true,
                'nombre_empresa' => 'Transportes Rápidos SA',
            ]
        );

        // Crear vehículos para los clientes
        \App\Models\Vehiculo::firstOrCreate(
            ['placa' => 'ABC-123'],
            [
                'cliente_id' => $cliente1->id,
                'marca' => 'Toyota',
                'modelo' => 'Corolla',
                'anio' => 2020,
                'color' => 'Rojo',
                'vin' => '1HGBH41JXMN109186',
            ]
        );

        \App\Models\Vehiculo::firstOrCreate(
            ['placa' => 'XYZ-789'],
            [
                'cliente_id' => $cliente2->id,
                'marca' => 'Ford',
                'modelo' => 'F-150',
                'anio' => 2021,
                'color' => 'Azul',
                'vin' => '1FTEW1EP4JFA12345',
            ]
        );
    }
}
