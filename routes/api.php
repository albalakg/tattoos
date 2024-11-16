<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventController;

Route::get('events/{id}', [EventController::class, 'find']);
Route::post('events/file', [EventController::class, 'uploadFile']);

Route::get('/{any?}', function () {
    return response()->json([
        'message' => 'Sorry, route does not exists',
        'data' => null,
        'status' => false,
    ], 404);
})
    ->where('any', '^(?!api\/).*')
    ->name('home');