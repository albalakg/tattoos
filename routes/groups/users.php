<?php

use App\Domain\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('create', [UserController::class, 'create']);
