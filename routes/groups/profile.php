<?php

use App\Domain\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('logout', [UserController::class, 'logout']);
Route::get('details', [UserController::class, 'details']);
Route::get('tattoos', [UserController::class, 'getTattoos']);
