<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['app.key' => Str::random(32)]);
        Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'profesor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'bibliotecario', 'guard_name' => 'web']);
    }

    #[Test]
    public function aplicacion_responde_200_en_raiz(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    #[Test]
    public function bibliotecario_puede_crear_prestamo_devuelve_201(): void
    {
        $book = Book::factory()->create([
            'total_copies' => 3,
            'available_copies' => 2,
            'is_available' => true,
        ]);

        $user = User::factory()->create();
        $user->assignRole('bibliotecario');

        $response = $this->actingAs($user)->postJson('/api/v1/loans', [
            'requester_name' => 'John Doe',
            'book_id' => $book->id,
        ]);

        $response->assertStatus(201);
    }

    #[Test]
    public function estudiante_no_puede_listar_prestamos_devuelve_403(): void
    {
        $user = User::factory()->create();
        $user->assignRole('estudiante');

        $response = $this->actingAs($user)->getJson('/api/v1/loans');

        $response->assertStatus(403);
    }
}
