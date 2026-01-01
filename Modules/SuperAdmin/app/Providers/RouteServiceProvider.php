<?php

namespace Modules\SuperAdmin\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected string $name = 'SuperAdmin';

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
     * These routes are only accessible from central domains and require authentication.
     */
    protected function mapApiRoutes(): void
    {
        foreach (config('tenancy.central_domains') as $domain) {
            Route::domain($domain)->group(function () {
                Route::prefix('api')->group(function () {
                    Route::prefix('super-admin')->middleware(['auth:sanctum', 'super.admin'])->group(function () {
                        require module_path($this->name, '/routes/api.php');
                    });
                });
            });
        }
    }
}
