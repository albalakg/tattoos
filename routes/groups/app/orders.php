<?php

use App\Domain\Content\Controllers\CouponController;
use App\Domain\Orders\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('coupon',    [CouponController::class, 'getCoupon'])->middleware('throttle:15,1');
Route::get('recent',    [OrderController::class, 'getRecentOrder'])->middleware('throttle:30,3');
Route::post('',         [OrderController::class, 'create'])->middleware('throttle:5,1');