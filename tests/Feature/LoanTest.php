<?php

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