<?php

namespace Tests\Unit;

use App\Models\Book;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tiene_los_atributos_fillable_esperados(): void
    {
        $book = new Book;

        $this->assertSame([
            'title',
            'description',
            'ISBN',
            'total_copies',
            'available_copies',
            'is_available',
        ], $book->getFillable());
    }

    #[Test]
    public function relacion_loans_devuelve_has_many(): void
    {
        $book = Book::factory()->create();

        $this->assertCount(0, $book->loans);

        Loan::factory()->create(['book_id' => $book->id, 'requester_name' => 'Usuario Test']);

        $book->refresh();
        $this->assertCount(1, $book->loans);
        $this->assertInstanceOf(Loan::class, $book->loans->first());
    }

    #[Test]
    public function puede_actualizar_un_libro_con_atributos_fillable(): void
    {
        // 1. PREPARACIÓN: Crear un libro en la base de datos con datos iniciales
        $book = Book::factory()->create([
            'title' => 'Título original',
            'description' => 'Descripción original',
            'ISBN' => '978-1-11-111111-1',
            'total_copies' => 5,
            'available_copies' => 3,
            'is_available' => true,
        ]);

        // 2. ACCIÓN: Simular lo que hace el controlador cuando alguien actualiza un libro
        $book->update([
            'title' => 'Título actualizado',
            'description' => 'Descripción actualizada',
            'total_copies' => 10,
            'available_copies' => 8,
            'is_available' => true,
        ]);

        // 3. Recargar el libro desde la BD para ver los datos guardados
        $book->refresh();

        // 4. VERIFICACIÓN: Comprobar que los cambios se guardaron correctamente
        $this->assertSame('Título actualizado', $book->title);
        $this->assertSame('Descripción actualizada', $book->description);
        $this->assertSame(10, $book->total_copies);
        $this->assertSame(8, $book->available_copies);
        $this->assertTrue($book->is_available);
    }

    #[Test]
    public function puede_eliminar_un_libro_y_ya_no_existe_en_la_base_de_datos(): void
    {
        // 1. PREPARACIÓN: Crear un libro y guardar su ID (después de delete() el objeto sigue existiendo en memoria)
        $book = Book::factory()->create([
            'title' => 'Libro a eliminar',
            'ISBN' => '978-2-22-222222-2',
        ]);
        $bookId = $book->id;

        // 2. ACCIÓN: Eliminar el libro (como hace el controlador destroy)
        $book->delete();

        // 3. VERIFICACIÓN: Comprobar que ya no está en la tabla 'books'
        $this->assertDatabaseMissing('books', ['id' => $bookId]);
        // Y que buscar por ID devuelve null (no encontrado)
        $this->assertNull(Book::find($bookId));
    }
}
