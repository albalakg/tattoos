<?php

use App\Domain\Content\Controllers\ChallengeController;
use Illuminate\Support\Facades\Route;

Route::post('create',   [ChallengeController::class, 'create']);
Route::post('delete',   [ChallengeController::class, 'delete']);
Route::get('',          [ChallengeController::class, 'getAll']);