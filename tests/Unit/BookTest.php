<?php

use App\Models\Book;
use App\Models\Loan;

it('tiene los atributos fillable correctos', function () {
    $data = [
        'title' => 'Domain-Driven Design',
        'description' => 'A blue book',
        'ISBN' => '1111111111',
        'total_copies' => 10,
        'available_copies' => 7,
        'is_available' => true,
    ];

    $book = new Book($data);

    expect($book->title)->toBe($data['title'])
        ->and($book->description)->toBe($data['description'])
        ->and($book->ISBN)->toBe($data['ISBN'])
        ->and($book->total_copies)->toBe($data['total_copies'])
        ->and($book->available_copies)->toBe($data['available_copies'])
        ->and($book->is_available)->toBe($data['is_available']);
});

it('tiene relación con préstamos', function () {
    $book = Book::factory()->create();
    $loan = Loan::factory()->create([
        'book_id' => $book->id,
    ]);

    $book->load('loans');

    expect($book->loans)->toHaveCount(1)
        ->and($book->loans->first())->toBeInstanceOf(Loan::class)
        ->and($book->loans->first()->id)->toBe($loan->id);
});

