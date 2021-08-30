<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::prefix('api/auth')
                ->namespace($this->getNamespace('Users'))
                ->middleware('throttle:100,10', 'guest')
                ->group(base_path("routes/groups/auth.php"));

            Route::prefix('api/profile')
                ->namespace($this->namespace)
                ->group(base_path("routes/groups/profile.php"));

            Route::prefix('api/users')
                ->namespace($this->namespace)
                ->group(base_path("routes/groups/users.php"));

            Route::prefix('api/tags')
                ->namespace($this->namespace)
                ->group(base_path("routes/groups/tags.php"));

            Route::prefix('api/cms')
                ->namespace($this->namespace)
                ->group(base_path("routes/groups/cms.php"));

        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60);
        });
    }
    
    /**
     * @param string $component
     * @return string
    */
    private function getNamespace(string $component)
    {
        return "App\\Domain\\$component\\Controllers";
    }
}
