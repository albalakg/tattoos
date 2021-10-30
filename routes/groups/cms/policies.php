<?php

use App\Domain\Orders\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cookies'], function () {
    Route::get('', [OrderController::class, 'getAll']);
});

Route::group(['prefix' => 'terms-and-conditions'], function () {
    Route::get('', [OrderController::class, 'getAll']);
});

Route::post('status/update', [OrderController::class, 'updateStatus']);
