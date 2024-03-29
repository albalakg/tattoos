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
                ->middleware('cache.headers:private;max_age=3600')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::prefix('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));
            });

        $this->setAppRoutes();
        $this->setTestsRoutes();
        $this->setCMSRoutes();
        $this->setGeneralRoutes();
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
    
    private function setAppRoutes()
    {
        Route::prefix('api/auth')
            ->namespace($this->getNamespace('Users'))
            ->middleware('throttle:100,10', 'guest')
            ->group(base_path("routes/groups/auth.php"));
            
        Route::prefix('api/profile')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/app/profile.php"));

        Route::prefix('api/support')
            ->middleware($this->optional_middleware)
            ->namespace($this->getNamespace('Users'))
            ->group(base_path("routes/groups/app/support.php"));

        Route::prefix('api/payment')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/app/payment.php"));

        Route::prefix('api/orders')
            ->middleware('auth:api')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/app/orders.php"));
        
        Route::prefix('api/content')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/app/content.php"));
            
        Route::prefix('api/policies')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/app/policies.php"));
    }
    
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

        Route::prefix('api/cms/challenges')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/challenges.php"));

        Route::prefix('api/cms/training-options')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/trainingOptions.php"));

        Route::prefix('api/cms/skills')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/skills.php"));

        Route::prefix('api/cms/terms')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/terms.php"));

        Route::prefix('api/cms/equipment')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/equipment.php"));

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

        Route::prefix('api/cms/marketing')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/marketingTokens.php"));

        Route::prefix('api/cms/support')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/support.php"));
            
        Route::prefix('api/cms/policies')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/policies.php"));

        Route::prefix('api/cms/users')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/users.php"));

        Route::prefix('api/cms/courses')
            ->middleware('auth:api', 'admin')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/cms/courses.php"));
    }
    
    private function setGeneralRoutes()
    {
        Route::prefix('api/general/logs')
            ->middleware('internalToken')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/general/logs.php"));
    }
    
    private function setTestsRoutes()
    {
        Route::prefix('tests/mails')
            ->middleware('testing')
            ->namespace($this->namespace)
            ->group(base_path("routes/groups/tests/mails.php"));
    }
}
