<?php

use App\Domain\Content\Controllers\CouponController;
use Illuminate\Support\Facades\Route;

Route::post('create',           [CouponController::class, 'create']);
Route::post('status/update',    [CouponController::class, 'updateStatus']);
Route::post('delete',           [CouponController::class, 'delete']);
Route::get('',                  [CouponController::class, 'getAll']);