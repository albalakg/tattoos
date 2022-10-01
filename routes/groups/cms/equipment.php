<?php

use App\Domain\Content\Controllers\EquipmentController;
use Illuminate\Support\Facades\Route;

Route::post('create', [EquipmentController::class, 'create']);
Route::post('update', [EquipmentController::class, 'update']);
Route::post('delete', [EquipmentController::class, 'delete']);
Route::get('', [EquipmentController::class, 'getAll']);
