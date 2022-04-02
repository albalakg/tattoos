<?php

use App\Domain\Content\Controllers\TrainerController;
use Illuminate\Support\Facades\Route;

Route::post('create',   [TrainerController::class, 'create']);
Route::post('update',   [TrainerController::class, 'update']);
Route::post('delete',   [TrainerController::class, 'delete']);
Route::get('',          [TrainerController::class, 'getAll']);