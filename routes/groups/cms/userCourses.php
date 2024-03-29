<?php

use App\Domain\Users\Controllers\UserCourseController;
use Illuminate\Support\Facades\Route;

Route::get('', [UserCourseController::class, 'getAll']);
Route::get('progress/{user_course_id}', [UserCourseController::class, 'getUserCourseProgress']);
Route::post('', [UserCourseController::class, 'create']);
Route::post('delete', [UserCourseController::class, 'delete']);

Route::group(['prefix' => 'tests'], function () {
    Route::get('', [UserCourseController::class, 'getAllTests']);
    Route::post('status/update', [UserCourseController::class, 'updateTestStatus']);
    Route::post('comment/create', [UserCourseController::class, 'createTestComment']);
});