<?php

use App\Http\Controllers\LoanController;
use App\Http\Requests\StoreLoanRequest;
use App\Models\Book;
use App\Models\Loan;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('no_permite_prestar_libro_sin_copias_disponibles', function () {
    $book = Book::factory()->create([
        'total_copies' => 1,
        'available_copies' => 0,
        'is_available' => false,
    ]);

    /** @var StoreLoanRequest $request */
    $request = StoreLoanRequest::create('/loans', 'POST', [
        'requester_name' => 'John Doe',
        'book_id' => $book->id,
    ]);

    $controller = new LoanController();
    $response = $controller->store($request);

    expect($response->getStatusCode())->toBe(422)
        ->and($response->getData(true)['message'])->toBe('Book is not available')
        ->and(Loan::count())->toBe(0);
});

it('crea_prestamo_correctamente_cuando_hay_copias', function () {
    $book = Book::factory()->create([
        'total_copies' => 3,
        'available_copies' => 2,
        'is_available' => true,
    ]);

    /** @var StoreLoanRequest $request */
    $request = StoreLoanRequest::create('/loans', 'POST', [
        'requester_name' => 'Jane Doe',
        'book_id' => $book->id,
    ]);

    $controller = new LoanController();
    $response = $controller->store($request);

    expect($response->getStatusCode())->toBe(201)
        ->and(Loan::count())->toBe(1);

    $loan = Loan::first();
    $book->refresh();

    expect($loan->isActive)->toBeTrue()
        ->and($book->available_copies)->toBe(1)
        ->and($book->is_available)->toBeTrue();
});

it('no_permite_devolver_libro_inexistente')->skip('Pendiente de implementar lógica de devolución');

it('no_permite_devolver_con_datos_invalidos')->skip('Pendiente de implementar lógica de devolución');

it('permite_devolver_prestamo_activo_correctamente')->skip('Pendiente de implementar lógica de devolución');

