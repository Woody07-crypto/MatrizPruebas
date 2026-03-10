<?php

use Illuminate\Support\Str;

test('the application returns a successful response', function () {
    config(['app.key' => Str::random(32)]);

    $response = $this->get('/');

    $response->assertStatus(200);
});
