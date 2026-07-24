<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RefaccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Refaccion::firstOrCreate(
            ['sku' => 'FIL-ACE-001'],
            [
                'nombre' => 'Filtro de Aceite',
                'descripcion' => 'Filtro de aceite para vehículos ligeros',
                'costo' => 50.00,
                'precio_venta' => 85.00,
                'stock_actual' => 20,
                'stock_minimo' => 5,
                'ubicacion' => 'A-1',
                'proveedor' => 'AutoParts SA',
                'activo' => true,
            ]
        );

        \App\Models\Refaccion::firstOrCreate(
            ['sku' => 'FRE-PAS-002'],
            [
                'nombre' => 'Pastillas de Freno',
                'descripcion' => 'Pastillas de freno delanteras',
                'costo' => 150.00,
                'precio_venta' => 250.00,
                'stock_actual' => 3,
                'stock_minimo' => 5,
                'ubicacion' => 'B-2',
                'proveedor' => 'BrakeMaster',
                'activo' => true,
            ]
        );

        \App\Models\Refaccion::firstOrCreate(
            ['sku' => 'BAT-12V-003'],
            [
                'nombre' => 'Batería 12V',
                'descripcion' => 'Batería automotriz 12V 50Ah',
                'costo' => 400.00,
                'precio_venta' => 650.00,
                'stock_actual' => 8,
                'stock_minimo' => 3,
                'ubicacion' => 'C-1',
                'proveedor' => 'PowerBattery',
                'activo' => true,
            ]
        );

        \App\Models\Refaccion::firstOrCreate(
            ['sku' => 'NEU-195-004'],
            [
                'nombre' => 'Neumático 195/65R15',
                'descripcion' => 'Neumático para vehículos compactos',
                'costo' => 300.00,
                'precio_venta' => 500.00,
                'stock_actual' => 15,
                'stock_minimo' => 4,
                'ubicacion' => 'D-3',
                'proveedor' => 'TireWorld',
                'activo' => true,
            ]
        );
    }
}
