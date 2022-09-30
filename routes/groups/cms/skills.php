<?php

use App\Domain\Content\Controllers\SkillController;
use Illuminate\Support\Facades\Route;

Route::post('create', [SkillController::class, 'create']);
Route::post('update', [SkillController::class, 'update']);
Route::post('delete', [SkillController::class, 'delete']);
Route::get('', [SkillController::class, 'getAll']);
