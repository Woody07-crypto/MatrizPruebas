<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;


    

    #[Test]
    public function detalle_libro_devuelve_libro_cuando_autenticado_y_existe(): void
    {
        // Preparacion
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'title' => 'El Quijote',
            'description' => 'Novela de Cervantes',
            'ISBN' => '978-84-376-0494-7',
            'total_copies' => 5,
            'available_copies' => 3,
            'is_available' => true,
        ]);

        // Ejecucion
        $response = $this->actingAs($user)->getJson("/api/v1/books/{$book->id}");

        // Verificacion
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

    #[Test]
    public function actualizar_libro_requiere_autenticacion(): void
    {
        $book = Book::factory()->create();

        $payload = [
            'title' => 'Clean Code',
            'description' => 'Libro sobre buenas practicas de programacion',
            'ISBN' => '9780132350884',
            'total_copies' => 10,
            'available_copies' => 8,
            'is_available' => true,
        ];

        $response = $this->putJson("/api/v1/books/{$book->id}", $payload);

        $response->assertStatus(401);
    }

    #[Test]
    public function actualizar_libro_con_usuario_autenticado(): void
    {
        Role::firstOrCreate(['name' => 'Bibliotecario', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('Bibliotecario');
        $book = Book::factory()->create([
            'title' => 'Titulo anterior',
            'description' => 'Descripcion anterior',
            'ISBN' => '1111111111',
            'total_copies' => 5,
            'available_copies' => 5,
            'is_available' => true,
        ]);

        $payload = [
            'title' => 'Clean Code',
            'description' => 'Libro sobre buenas practicas de programacion',
            'ISBN' => '9780132350884',
            'total_copies' => 10,
            'available_copies' => 8,
            'is_available' => true,
        ];

        $response = $this->actingAs($user)->putJson("/api/v1/books/{$book->id}", $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'id' => $book->id,
            'title' => 'Clean Code',
            'description' => 'Libro sobre buenas practicas de programacion',
            'ISBN' => '9780132350884',
            'total_copies' => 10,
            'available_copies' => 8,
            'is_available' => 'Disponible',
        ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => 'Clean Code',
            'description' => 'Libro sobre buenas practicas de programacion',
            'ISBN' => '9780132350884',
            'total_copies' => 10,
            'available_copies' => 8,
            'is_available' => true,
        ]);
    }

    #[Test]
    public function borrar_libro_requiere_autenticacion(): void
    {
        $book = Book::factory()->create();

        $response = $this->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(401);
    }

    #[Test]
    public function borrar_libro_con_usuario_autenticado(): void
    {
        Role::firstOrCreate(['name' => 'Bibliotecario', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('Bibliotecario');
        $book = Book::factory()->create();

        $response = $this->actingAs($user)->deleteJson("/api/v1/books/{$book->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);
    }

    // --- Tests de creaci?n de libro (seg?n especificaci?n) ---

    #[Test]
    public function bibliotecario_crea_libro_con_datos_correctos_devuelve_201(): void
    {
        Role::firstOrCreate(['name' => 'Bibliotecario', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('Bibliotecario');

        $payload = [
            'title' => 'El Programador Pragm?tico',
            'description' => 'De aprendiz a maestro',
            'ISBN' => '978-0201616224',
            'total_copies' => 3,
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/books', $payload);

        $response->assertStatus(201);
        $response->assertJson([
            'title' => 'El Programador Pragm?tico',
            'description' => 'De aprendiz a maestro',
            'ISBN' => '978-0201616224',
            'total_copies' => 3,
            'available_copies' => 3,
            'is_available' => 'Disponible',
        ]);
        $this->assertDatabaseHas('books', [
            'title' => 'El Programador Pragm?tico',
            'ISBN' => '978-0201616224',
            'total_copies' => 3,
            'available_copies' => 3,
            'is_available' => true,
        ]);
    }

    #[Test]
    public function estudiante_intenta_crear_libro_devuelve_403(): void
    {
        Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('estudiante');

        $payload = [
            'title' => 'El Programador Pragm?tico',
            'description' => 'De aprendiz a maestro',
            'ISBN' => '978-0201616224',
            'total_copies' => 3,
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/books', $payload);

        $response->assertStatus(403);
    }

    #[Test]
    public function profesor_intenta_crear_libro_devuelve_403(): void
    {
        Role::firstOrCreate(['name' => 'profesor', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('profesor');

        $payload = [
            'title' => 'El Programador Pragm?tico',
            'description' => 'De aprendiz a maestro',
            'ISBN' => '978-0201616224',
            'total_copies' => 3,
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/books', $payload);

        $response->assertStatus(403);
    }

    #[Test]
    public function bibliotecario_crea_libro_sin_datos_obligatorios_devuelve_422(): void
    {
        Role::firstOrCreate(['name' => 'Bibliotecario', 'guard_name' => 'web']);
        $user = User::factory()->create();
        $user->assignRole('Bibliotecario');

        $payload = [
            'description' => 'Libro sin t?tulo',
            'total_copies' => 5,
        ];

        $response = $this->actingAs($user)->postJson('/api/v1/books', $payload);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['title', 'ISBN']);
    }
}
