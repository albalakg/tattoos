<?php

use App\Domain\Content\Controllers\TermController;
use Illuminate\Support\Facades\Route;

Route::post('create', [TermController::class, 'create']);
Route::post('update', [TermController::class, 'update']);
Route::post('delete', [TermController::class, 'delete']);
Route::get('', [TermController::class, 'getAll']);
