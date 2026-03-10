<?php

use App\Http\Resources\BookResource;
use App\Models\Book;

it('transforma el detalle de libro a JSON con libro disponible', function () {
    $book = Book::factory()->make([
        'title' => 'Clean Code',
        'description' => 'A book about writing clean code',
        'ISBN' => '1234567890',
        'total_copies' => 5,
        'available_copies' => 3,
        'is_available' => true,
    ]);

    $resource = (new BookResource($book))->toArray(request());

    expect($resource)->toMatchArray([
        'id' => $book->id,
        'title' => 'Clean Code',
        'description' => 'A book about writing clean code',
        'ISBN' => '1234567890',
        'total_copies' => 5,
        'available_copies' => 3,
        'is_available' => 'Disponible',
    ]);
});

it('transforma el detalle de libro a JSON con libro no disponible', function () {
    $book = Book::factory()->make([
        'title' => 'Refactoring',
        'description' => 'A book about refactoring',
        'ISBN' => '0987654321',
        'total_copies' => 1,
        'available_copies' => 0,
        'is_available' => false,
    ]);

    $resource = (new BookResource($book))->toArray(request());

    expect($resource['is_available'])->toBe('No Disponible');
});

