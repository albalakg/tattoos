<?php

use App\Domain\Users\Controllers\UserController;
use Illuminate\Support\Facades\Route;


Route::post('delete-response', [UserController::class, 'deleteResponse']);

Route::group(['middleware' => 'auth:api'], function () {

    Route::post('logout', [UserController::class, 'logout']);
    Route::post('change-password', [UserController::class, 'changePassword']);
    Route::post('update/email', [UserController::class, 'updateEmail']);
    
});


