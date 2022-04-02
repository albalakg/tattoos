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
     * indicates if the authorization middleware should be activated
     *
     * @var mixed
    */
    protected $optional_middleware;

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->optional_middleware = request()->header('authorization') ? 'auth:api' : 'guest';

        $this->configureRateLimiting();
        $this->routes(function () {
            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
            });

        $this->setAppRoutes();
        $this->setCMSRoutes();
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
    
    /**
     * @return void
    */
    private function setAppRoutes()
    {
        Route::prefix('api/auth')
            ->namespace($this->getNamespace('Users'))
            ->middleware('throttle:100,10', 'guest')
            ->group(base_path("routes/groups/auth.php"));
            
        Route::prefix('api/trainers')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/app/trainers.php"));
            
        Route::prefix('api/profile')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/app/profile.php"));

        Route::prefix('api/users')
            ->middleware('auth:api')
            ->namespace($this->getNamespace('Users'))
            ->group(base_path("routes/groups/app/users.php"));

        Route::prefix('api/support')
            ->middleware($this->optional_middleware)
            ->namespace($this->getNamespace('Users'))
            ->group(base_path("routes/groups/app/support.php"));

        Route::prefix('api/orders')
            ->middleware($this->optional_middleware)
            ->namespace($this->getNamespace('Users'))
            ->group(base_path("routes/groups/app/orders.php"));
        
        Route::prefix('api/course-categories')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/app/coursesCategories.php"));
    }
    
    /**
     * @return void
    */
    private function setCMSRoutes()
    {
        Route::prefix('api/cms/course-areas')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/courseAreas.php"));

        Route::prefix('api/cms/lessons')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/lessons.php"));

        Route::prefix('api/cms/course-categories')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/courseCategories.php"));

        Route::prefix('api/cms/videos')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/videos.php"));

        Route::prefix('api/cms/coupons')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/coupons.php"));

        Route::prefix('api/cms/user-courses')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/userCourses.php"));

        Route::prefix('api/cms/trainers')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/trainers.php"));

        Route::prefix('api/cms/orders')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/orders.php"));

        Route::prefix('api/cms/support')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/support.php"));
            
        Route::prefix('api/cms/policies')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/policies.php"));
            
        Route::prefix('api/cms/tags')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/tags.php"));

        Route::prefix('api/cms/users')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/users.php"));

        Route::prefix('api/cms/courses')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/courses.php"));
    }
}
