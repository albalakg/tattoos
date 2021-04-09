<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')
    ->namespace('groups/auth');

Route::prefix('users')
    ->namespace('groups/users');

Route::prefix('tags')
    ->namespace('groups/tags');

Route::prefix('studios')
    ->namespace('groups/studios');

Route::prefix('studios')
    ->namespace('groups/studios');

Route::prefix('cms')
    ->namespace('groups/cms')
    ->middleware();

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
