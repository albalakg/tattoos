<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Support\Controllers\SupportController;
use App\Domain\Support\Controllers\SupportCategoryController;

Route::group(['prefix' => 'tickets'], function () {
    Route::get('', [SupportController::class, 'getAll']);
});

Route::group(['prefix' => 'categories'], function () {
    Route::get('', [SupportCategoryController::class, 'getAll']);
    Route::post('create', [SupportCategoryController::class, 'create']);
    Route::post('delete', [SupportCategoryController::class, 'multipleDelete']);
});