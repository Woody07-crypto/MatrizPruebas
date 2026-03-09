<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Crea el rol Bibliotecario y un usuario de prueba para usar en Postman.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'Bibliotecario']);

        $user = User::firstOrCreate(
            ['email' => 'bibliotecario@test.com'],
            [
                'name' => 'Bibliotecario Test',
                'password' => Hash::make('password'),
            ]
        );

        $user->assignRole('Bibliotecario');
    }
}
