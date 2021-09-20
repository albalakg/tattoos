<?php

use App\Domain\Content\Controllers\CourseLessonController;
use Illuminate\Support\Facades\Route;

Route::post('create', [CourseLessonController::class, 'create']);
Route::post('update', [CourseLessonController::class, 'update']);
Route::post('delete', [CourseLessonController::class, 'delete']);
Route::get('', [CourseLessonController::class, 'getAll']);