<?php

use App\Domain\Policies\Controllers\TermsAndConditionsController;
use Illuminate\Support\Facades\Route;

Route::get('terms-and-conditions', [TermsAndConditionsController::class, 'getCurrent']);