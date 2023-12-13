<?php

use App\Domain\Helpers\ThrottleService;
use App\Domain\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('delete-response', [UserController::class, 'deleteResponse']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::get('',                                  [UserController::class, 'getProfile']);
    Route::get('courses',                           [UserController::class, 'getUserActiveCourses']);
    Route::get('favorites',                         [UserController::class, 'getUserFavoriteContent']);
    Route::get('progress',                          [UserController::class, 'getUserProgress']);
    Route::get('orders',                            [UserController::class, 'getUserOrders']);
    Route::get('challenges',                        [UserController::class, 'getUserChallenges']);
    Route::get('support-tickets',                   [UserController::class, 'getUserSupportTickets']);
    Route::get('landed-on-page-not-found',          [UserController::class, 'landedOnPageNotFound']);
    
    Route::post('logout',                           [UserController::class, 'logout']);
    Route::post('change-password',                  [UserController::class, 'changePassword'])->middleware(ThrottleService::getProfileThrottle());
    Route::post('email',                            [UserController::class, 'updateEmail'])->middleware(ThrottleService::getProfileThrottle());
    Route::post('update',                           [UserController::class, 'updateProfile'])->middleware(ThrottleService::getProfileThrottle());
    Route::post('favorites/add',                    [UserController::class, 'addToFavorite']);
    Route::post('favorites/remove',                 [UserController::class, 'removeFromFavorite']);
    Route::post('lesson/progress',                  [UserController::class, 'setLessonProgress']);
    Route::post('lesson/schedule',                  [UserController::class, 'scheduleLesson']);
    Route::post('lesson/training-schedule',         [UserController::class, 'addTrainingSchedule']);
    Route::post('lesson/training-schedule/{id}',    [UserController::class, 'updateTrainingSchedule']);
    Route::post('challenge/submit/{id}',            [UserController::class, 'submitChallenge']);
    Route::delete('lesson/training-schedule/{id}',  [UserController::class, 'deleteTrainingSchedule']);
    
});