<?php

use App\Domain\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('logout', [UserController::class, 'logout']);
Route::post('change-password', [UserController::class, 'changePassword']);
Route::post('delete-account-request', [UserController::class, 'deleteRequest']);
Route::post('delete-account-response', [UserController::class, 'deleteResponse']);
Route::post('update/email', [UserController::class, 'updateEmail']);

Route::get('details', [UserController::class, 'details']);
Route::get('tattoos', [UserController::class, 'getTattoos']);
