<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

$namespace = 'App\Domain';
$group = 'routes/groups';

Route::prefix('auth')
    ->namespace($namespace)
    ->middleware('throttle:5,1', 'guest')
    ->group(base_path("$group/auth.php"));

Route::prefix('profile')
    ->namespace($namespace)
    ->middleware('auth:api')
    ->group(base_path("$group/profile.php"));

Route::prefix('users')
    ->namespace($namespace)
    ->group(base_path("$group/users.php"));

Route::prefix('tags')
    ->namespace($namespace)
    ->group(base_path("$group/tags.php"));

Route::prefix('studios')
    ->namespace($namespace)
    ->group(base_path("$group/studios.php"));

Route::prefix('studios')
    ->namespace($namespace)
    ->group(base_path("$group/studios.php"));

Route::prefix('cms')
    ->namespace($namespace)
    ->group(base_path("$group/cms.php"));
