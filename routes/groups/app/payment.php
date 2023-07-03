<?php

use App\Domain\Orders\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::post('success',  [OrderController::class, 'success']);
Route::post('failure',  [OrderController::class, 'failure']);
Route::post('callback', [OrderController::class, 'callback']);
Route::get('success',   [OrderController::class, 'success']);
Route::get('failure',   [OrderController::class, 'failure']);
Route::get('callback',  [OrderController::class, 'callback']);