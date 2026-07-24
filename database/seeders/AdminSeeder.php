<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::firstOrCreate(
            ['email' => 'admin@taller.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
                'role' => 'admin',
            ]
        );

        \App\Models\User::firstOrCreate(
            ['email' => 'mecanico@taller.com'],
            [
                'name' => 'Mecánico Juan',
                'password' => bcrypt('password'),
                'role' => 'mecanico',
            ]
        );
    }
}
