<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function () {
    return response()->json([
        'message' => 'Sorry, route does not exists',
        'data' => null,
        'status' => false,
    ], 404);
})
    ->where('any', '^(?!api\/).*')
    ->name('home');