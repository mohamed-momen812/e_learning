<?php

namespace Modules\Admin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'Admin';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')->group(function () {
            require module_path($this->name, '/routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are tenant-specific and require tenant middleware.
     */
    protected function mapApiRoutes(): void
    {
        Route::middleware([
            'api',
            InitializeTenancyBySubdomain::class,
            PreventAccessFromCentralDomains::class,
        ])->prefix('api')->group(function () {
            Route::prefix('admin')->middleware('auth:sanctum')->group(function () {
                require module_path($this->name, '/routes/api.php');
            });
        });
    }
}
