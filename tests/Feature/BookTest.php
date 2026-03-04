<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function detalle_libro_devuelve_libro_cuando_autenticado_y_existe(): void
    {
        // Preparación
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'title' => 'El Quijote',
            'description' => 'Novela de Cervantes',
            'ISBN' => '978-84-376-0494-7',
            'total_copies' => 5,
            'available_copies' => 3,
            'is_available' => true,
        ]);

        // Ejecución
        $response = $this->actingAs($user)->getJson("/api/v1/books/{$book->id}");

        // Verificación
        $response->assertStatus(200);
        $response->assertJson([
            'id' => $book->id,
            'title' => 'El Quijote',
            'description' => 'Novela de Cervantes',
            'ISBN' => '978-84-376-0494-7',
            'total_copies' => 5,
            'available_copies' => 3,
            'is_available' => 'Disponible',
        ]);
        $response->assertJsonStructure([
            'id',
            'title',
            'description',
            'ISBN',
            'total_copies',
            'available_copies',
            'is_available',
        ]);
    }

    #[Test]
    public function detalle_libro_devuelve_404_cuando_el_libro_no_existe(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/v1/books/99999');

        $response->assertStatus(404);
    }

    #[Test]
    public function detalle_libro_requiere_autenticacion(): void
    {
        $book = Book::factory()->create();

        $response = $this->getJson("/api/v1/books/{$book->id}");

        $response->assertStatus(401);
    }
}
