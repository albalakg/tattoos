<?php

use App\Domain\Content\Controllers\CourseCategoryController;
use App\Domain\Content\Controllers\CourseController;
use App\Domain\Content\Controllers\CourseLessonController;
use App\Domain\Content\Controllers\TrainerController;
use Illuminate\Support\Facades\Route;

Route::get('course-categories', [CourseCategoryController::class, 'getActive']);
Route::get('lessons', [CourseLessonController::class, 'getRandomActiveLessons']);
Route::get('trainers', [TrainerController::class, 'getTrainersForApp']);
Route::get('courses', [CourseController::class, 'getGuestActiveCourses']);
Route::get('courses/{id}', [CourseController::class, 'getCourse']);