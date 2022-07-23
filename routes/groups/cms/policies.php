<?php

use App\Domain\Policies\Controllers\TermsAndConditionsController;
use Illuminate\Support\Facades\Route;

// Route::group(['prefix' => 'cookies'], function () {
// TODO
// });

Route::group(['prefix' => 'terms-and-conditions'], function () {
    Route::get('', [TermsAndConditionsController::class, 'getAll']);
    Route::post('create', [TermsAndConditionsController::class, 'create']);
});