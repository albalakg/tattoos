<?php

use App\Domain\Content\Controllers\TrainerController;
use Illuminate\Support\Facades\Route;

Route::get('', [TrainerController::class, 'getTrainersForApp']);
