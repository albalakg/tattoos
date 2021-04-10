<?php

use App\Domain\Users\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('signup', [AuthController::class, 'signup']);
Route::post('login', [AuthController::class, 'login']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);
Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('email-verification', [AuthController::class, 'verifyEmail']);
