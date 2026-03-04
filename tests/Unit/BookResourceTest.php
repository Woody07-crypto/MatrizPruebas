<?php

namespace Tests\Unit;

use App\Http\Resources\BookResource;
use App\Models\Book;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookResourceTest extends TestCase
{
    #[Test]
    public function transforma_libro_a_array_con_estructura_de_detalle(): void
    {
        $book = new Book([
            'id' => 1,
            'title' => 'Cien años de soledad',
            'description' => 'Novela de García Márquez',
            'ISBN' => '978-3-16-148410-0',
            'total_copies' => 10,
            'available_copies' => 7,
            'is_available' => true,
        ]);

        $resource = new BookResource($book);
        $request = Request::create('/');
        $array = $resource->toArray($request);

        $this->assertSame(1, $array['id']);
        $this->assertSame('Cien años de soledad', $array['title']);
        $this->assertSame('Novela de García Márquez', $array['description']);
        $this->assertSame('978-3-16-148410-0', $array['ISBN']);
        $this->assertSame(10, $array['total_copies']);
        $this->assertSame(7, $array['available_copies']);
        $this->assertSame('Disponible', $array['is_available']);
    }

    #[Test]
    public function transforma_is_available_a_no_disponible_cuando_es_false(): void
    {
        $book = new Book([
            'id' => 2,
            'title' => 'Libro agotado',
            'description' => 'Sin ejemplares',
            'ISBN' => '111-1-11-111111-1',
            'total_copies' => 1,
            'available_copies' => 0,
            'is_available' => false,
        ]);

        $resource = new BookResource($book);
        $array = $resource->toArray(Request::create('/'));

        $this->assertSame('No Disponible', $array['is_available']);
    }

    #[Test]
    public function incluye_todas_las_claves_esperadas_en_el_detalle(): void
    {
        $book = new Book([
            'id' => 3,
            'title' => 'Título',
            'description' => 'Desc',
            'ISBN' => '999',
            'total_copies' => 1,
            'available_copies' => 1,
            'is_available' => true,
        ]);

        $array = (new BookResource($book))->toArray(Request::create('/'));

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('title', $array);
        $this->assertArrayHasKey('description', $array);
        $this->assertArrayHasKey('ISBN', $array);
        $this->assertArrayHasKey('total_copies', $array);
        $this->assertArrayHasKey('available_copies', $array);
        $this->assertArrayHasKey('is_available', $array);
        $this->assertCount(7, $array);
    }
}
