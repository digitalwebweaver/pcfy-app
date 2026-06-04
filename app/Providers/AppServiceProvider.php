<?php

namespace App\Providers;

use App\Http\Controllers\Site\HomeController;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Fix for XAMPP subdirectory deployment (APP_URL = http://localhost/pcfy-app/public).
        //
        // Problem: Laravel's CompiledRouteCollection::requestWithoutTrailingSlash() strips the
        // trailing slash from REQUEST_URI (/pcfy-app/public/ → /pcfy-app/public). Without the
        // slash, Symfony's prepareBaseUrl() can no longer identify the base path, so pathInfo
        // becomes /pcfy-app/public instead of /. The compiled matcher finds nothing and falls
        // back to the dynamic RouteCollection. This booted() callback runs after the route
        // cache is loaded (which replaces the router's collection with CompiledRouteCollection),
        // so the dynamic GET / registered here lands in the right dynamic bucket and is found
        // by the fallback, resolving the 405 MethodNotAllowed on the homepage.
        Paginator::defaultView('pagination.default');

        $this->app->booted(function () {
            $this->app['router']
                ->middleware('web')
                ->get('/', [HomeController::class, 'index'])
                ->name('home');
        });
    }
}
