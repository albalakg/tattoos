<?php

use App\Domain\Orders\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('callback',  [OrderController::class, 'callback']);
Route::post('callback',  [OrderController::class, 'callback']);