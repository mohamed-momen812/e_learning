<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Policies\PermissionPolicy;
use App\Policies\RolePolicy;
use App\Policies\TenantPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Tenant::class => TenantPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Policies are auto-discovered in Laravel 11+
        // The $policies array is used for explicit policy mapping

        // // Telescope authorization - only allow access in TELESCOPE_ENABLED=true
        Gate::define('viewTelescope', function ($user = null) {
            return config('telescope.enabled');
        });
    }
}
