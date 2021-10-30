<?php

use App\Domain\Content\Controllers\CourseController;
use Illuminate\Support\Facades\Route;

Route::post('create',   [CourseController::class, 'create']);
Route::post('update',   [CourseController::class, 'update']);
Route::post('delete',   [CourseController::class, 'delete']);
Route::get('',          [CourseController::class, 'getAll']);