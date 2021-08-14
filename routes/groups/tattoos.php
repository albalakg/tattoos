<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Tattoos\Controllers\TattooController;

Route::get('', [TattooController::class, 'index']);
Route::get('show', [TattooController::class, 'show']);
Route::get('edit', [TattooController::class, 'edit']);
Route::post('create', [TattooController::class, 'create']);
Route::post('update', [TattooController::class, 'update']);
Route::post('delete', [TattooController::class, 'delete']);
