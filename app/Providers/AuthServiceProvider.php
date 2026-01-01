<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Tenant;
use App\Policies\UserPolicy;
use App\Policies\TenantPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

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
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Policies are auto-discovered in Laravel 11+
        // The $policies array is used for explicit policy mapping
    }
}

