<?php

use Illuminate\Support\Facades\Route; 

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ai', function () {

    $response = \Laravel\Ai\agent(
        instructions: 'You are a helpful assistant that provides information about the digital marketing restaurant.'
    )->prompt('What is the best strategy for social media marketing?');

    return response()->json([
        'response' => $response,
    ]);

});