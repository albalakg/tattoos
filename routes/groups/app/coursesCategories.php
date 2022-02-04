<?php

use App\Domain\Content\Controllers\CourseCategoryController;
use Illuminate\Support\Facades\Route;

Route::get('', [CourseCategoryController::class, 'getActive']);