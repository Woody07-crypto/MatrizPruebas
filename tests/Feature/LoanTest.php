<?php

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['app.key' => Str::random(32)]);
    Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'profesor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'bibliotecario', 'guard_name' => 'web']);
});

test('Loan', function () {
    $response = $this->get('/');
    $response->assertStatus(200);
});

it('can make a loan', function () {
    $book = Book::factory()->create([
        'total_copies' => 3,
        'available_copies' => 2,
        'is_available' => true,
    ]);
    $user = User::factory()->create()->assignRole('bibliotecario');

    $response = $this->actingAs($user)->postJson('/api/v1/loans', [
        'requester_name' => 'John Doe',
        'book_id' => $book->id,
    ]);

    $response->assertStatus(201);
});

it('cannot list loans if student role', function () {
    $user = User::factory()->create()->assignRole('estudiante');

    $response = $this->actingAs($user)->getJson('/api/v1/loans');

    $response->assertStatus(403);
});


