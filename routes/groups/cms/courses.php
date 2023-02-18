<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Content\Controllers\CourseController;
use App\Domain\Content\Controllers\CourseRecommendationController;

Route::get('',          [CourseController::class, 'getAll']);
Route::post('create',   [CourseController::class, 'create']);
Route::post('update',   [CourseController::class, 'update']);
Route::post('delete',   [CourseController::class, 'delete']);
Route::post('schedule', [CourseController::class, 'schedule']);

Route::group(['prefix' => 'recommendations'], function () {
    
    Route::post('create',   [CourseRecommendationController::class, 'create']);
    Route::post('delete',   [CourseRecommendationController::class, 'delete']);

});