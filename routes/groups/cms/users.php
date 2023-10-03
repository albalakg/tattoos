<?php

use App\Domain\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('create', [UserController::class, 'create']);
Route::post('update', [UserController::class, 'updateUser']);
Route::post('delete', [UserController::class, 'delete']);
Route::post('update/email', [UserController::class, 'updateUserEmail']);
Route::post('update/password', [UserController::class, 'updateUserPassword']);
Route::get('', [UserController::class, 'getAll']);
Route::get('{id}/login-history', [UserController::class, 'getActionLogsHistory']);
Route::get('{id}/challenges', [UserController::class, 'getUserChallengesHistory']);
