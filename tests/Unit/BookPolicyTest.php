<?php

use App\Models\Book;
use App\Models\User;
use App\Policies\BookPolicy;

it('permite listar libros', function () {
    $user = User::factory()->create();
    $policy = new BookPolicy();

    expect($policy->viewAny($user))->toBeTrue();
});

it('permite ver el detalle de un libro', function () {
    $user = User::factory()->create();
    $book = Book::factory()->make();
    $policy = new BookPolicy();

    expect($policy->view($user, $book))->toBeTrue();
});

