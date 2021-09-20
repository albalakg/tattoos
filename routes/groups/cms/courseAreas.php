<?php

use App\Domain\Content\Controllers\CourseAreaController;
use Illuminate\Support\Facades\Route;

Route::post('create', [CourseAreaController::class, 'create']);
Route::post('update', [CourseAreaController::class, 'update']);
Route::post('delete', [CourseAreaController::class, 'delete']);
Route::get('', [CourseAreaController::class, 'getAll']);