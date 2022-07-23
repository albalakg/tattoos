<?php

use App\Domain\Content\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::post('create', [VideoController::class, 'create']);
Route::post('update', [VideoController::class, 'update']);
Route::post('delete', [VideoController::class, 'delete']);
Route::get('', [VideoController::class, 'getAll']);
