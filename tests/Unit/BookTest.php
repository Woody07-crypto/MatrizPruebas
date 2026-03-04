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
}
