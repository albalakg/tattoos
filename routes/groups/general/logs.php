<?php

use Illuminate\Support\Facades\Route;
use App\Domain\General\Controllers\LogController;

Route::post('backup', [LogController::class, 'backup']);