<?php

use App\Domain\Content\Controllers\CouponController;
use App\Domain\Orders\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('coupon',    [CouponController::class, 'getCoupon'])->middleware('throttle:15,1');
Route::post('',         [OrderController::class, 'create'])->middleware('throttle:5,1');
Route::post('success', [OrderController::class, 'success']);
Route::post('failure', [OrderController::class, 'failure']);
Route::post('callback', [OrderController::class, 'callback']);

// https://payments.payplus.co.il/8ffca0f9-9369-40cb-a323-5fb6e2066bb6