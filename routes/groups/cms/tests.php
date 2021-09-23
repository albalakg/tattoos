<?php

use App\Domain\Content\Controllers\TestController;
use Illuminate\Support\Facades\Route;

Route::post('create', [TestController::class, 'create']);
Route::post('update', [TestController::class, 'update']);
Route::post('delete', [TestController::class, 'delete']);
Route::get('', [TestController::class, 'getAll']);
