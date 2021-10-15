<?php

use App\Domain\Orders\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('', [OrderController::class, 'getAll']);
Route::post('status/update', [OrderController::class, 'updateStatus']);
