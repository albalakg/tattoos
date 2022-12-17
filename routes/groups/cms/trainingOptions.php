<?php

use App\Domain\Content\Controllers\TrainingOptionController;
use Illuminate\Support\Facades\Route;

Route::post('create', [TrainingOptionController::class, 'create']);
Route::post('update', [TrainingOptionController::class, 'update']);
Route::post('delete', [TrainingOptionController::class, 'delete']);
Route::get('', [TrainingOptionController::class, 'getAll']);
