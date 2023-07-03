<?php

namespace App\Providers;

use Illuminate\Support\Str;
use App\Domain\Helpers\LogService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Cache::driver('array')->rememberForever(LogService::TRACK_ID, function() { 
            return Str::uuid();
        });
    }
}
