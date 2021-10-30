<?php

use App\Domain\Content\Controllers\CourseCategoryController;
use Illuminate\Support\Facades\Route;

Route::post('create',   [CourseCategoryController::class, 'create']);
Route::post('update',   [CourseCategoryController::class, 'update']);
Route::post('delete',   [CourseCategoryController::class, 'delete']);
Route::get('',          [CourseCategoryController::class, 'getAll']);
