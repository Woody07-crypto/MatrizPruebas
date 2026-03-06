<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_puede_iniciar_sesion()
    {
        $user = User::factory()->create([
            'email' => 'estudiante@biblioteca.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'estudiante@biblioteca.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200);
        
        $response->assertJsonStructure(['access_token']); 
    }

    public function test_usuario_no_puede_iniciar_sesion_con_credenciales_incorrectas()
    {
        User::factory()->create([
            'email' => 'estudiante@biblioteca.com',
            'password' => bcrypt('password123'),
        ]);

        
        $response = $this->postJson('/api/v1/login', [
            'email' => 'estudiante@biblioteca.com',
            'password' => 'claveEquivocada',
        ]);

        
        $response->assertStatus(422); 
    }

    public function test_usuario_puede_cerrar_sesion()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/logout');

        $response->assertStatus(200);
    }

    public function test_usuario_puede_ver_su_perfil()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson('/api/v1/profile');

        $response->assertStatus(200);
    }
}