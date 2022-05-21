<?php

use App\Domain\Orders\Controllers\MarketingTokenController;
use Illuminate\Support\Facades\Route;

Route::get('', [MarketingTokenController::class, 'getAll']);
Route::post('update', [MarketingTokenController::class, 'update']);
Route::post('create', [MarketingTokenController::class, 'create']);
Route::post('delete', [MarketingTokenController::class, 'delete']);