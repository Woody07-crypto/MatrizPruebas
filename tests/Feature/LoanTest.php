<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'profesor', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'bibliotecario', 'guard_name' => 'web']);
});

test('Loan', function () {
    $response = $this->get('');
    $response->assertStatus(200);
});

it ('can make a loan', function (){
    $response = $this -> post ('/loans', [
        'name' => 'John Doe',
        'book_id' => 1,
    ]);
    $response->assertStatus(200);
    $response -> assertJson([
        'message' => 'Loan created successfully', 
    ]);

});

it ('cannot list loans if student role', function (){

    $user = User::factory()->create()->assignRole('estudiante');

    $response = $this -> actingAs($user) -> getJson('/loans');
    $response->assertStatus(403);
    $response->assertJson([
        'message' => 'Unauthorized',
    ]);

});

