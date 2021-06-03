<?php

use Illuminate\Support\Facades\Route;

Route::post('signup', [TattooController::class, 'signup']);
Route::post('login', [TattooController::class, 'login']);
Route::post('reset-password', [TattooController::class, 'resetPassword']);
Route::post('forgot-password', [TattooController::class, 'forgotPassword']);
Route::post('email-verification', [TattooController::class, 'verifyEmail']);
