<?php

use Illuminate\Support\Facades\Route;
use App\Domain\Support\Controllers\SupportController;
use App\Domain\Support\Controllers\SupportCategoryController;


Route::get('categories', [SupportCategoryController::class, 'getActive']);
Route::post('create', [SupportController::class, 'create']);
Route::post('message/create', [SupportController::class, 'createSupportTicketMessage']);
