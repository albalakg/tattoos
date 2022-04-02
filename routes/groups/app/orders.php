<?php

use App\Domain\Content\Controllers\CouponController;
use Illuminate\Support\Facades\Route;

Route::get('coupon', [CouponController::class, 'getCoupon'])->middleware('throttle:5,1');
