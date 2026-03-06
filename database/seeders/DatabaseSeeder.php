<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles solo si no existen
        Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'profesor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'bibliotecario', 'guard_name' => 'web']);

    }
}