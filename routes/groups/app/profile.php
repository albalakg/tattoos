<?php

use App\Domain\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::post('delete-response', [UserController::class, 'deleteResponse']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::get('', [UserController::class, 'getProfile']);
    Route::get('courses', [UserController::class, 'getUserActiveCourses']);
    Route::get('favorites', [UserController::class, 'getUserFavoriteContent']);
    Route::get('progress', [UserController::class, 'getUserProgress']);
    Route::get('orders', [UserController::class, 'getUserOrders']);
    Route::get('support-tickets', [UserController::class, 'getUserSupportTickets']);
    
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('change-password', [UserController::class, 'changePassword'])->middleware('throttle:3,10');
    Route::post('email', [UserController::class, 'updateEmail']);
    Route::post('favorites/add', [UserController::class, 'addToFavorite']);
    Route::post('favorites/remove', [UserController::class, 'removeFromFavorite']);
    
});