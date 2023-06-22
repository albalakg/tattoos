<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Content\Controllers\CourseController;
use App\Domain\Content\Controllers\TrainerController;
use App\Domain\Content\Controllers\CourseLessonController;
use App\Domain\Orders\Controllers\MarketingTokenController;
use App\Domain\Content\Controllers\CourseCategoryController;

Route::get('course-categories',         [CourseCategoryController::class, 'getActive']);
Route::get('lessons',                   [CourseLessonController::class, 'getRandomActiveLessons']);
Route::get('trainers',                  [TrainerController::class, 'getTrainersForApp']);
Route::get('courses',                   [CourseController::class, 'getGuestActiveCourses']);
Route::get('courses/{id}',              [CourseController::class, 'getCourse']);
Route::get('marketing-token/{token}',   [MarketingTokenController::class, 'getByToken']);
