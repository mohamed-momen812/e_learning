<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| These routes are accessible via tenant subdomain (e.g., test.e_learning.test)
| The middleware automatically initializes tenant context.
|
*/

Route::middleware([
    'api',
    InitializeTenancyBySubdomain::class,
    PreventAccessFromCentralDomains::class,
])->prefix('api')->group(function () {
    // Get current tenant info
    Route::get('/tenant/info', function () {
        $tenant = tenant();
        return response()->json([
            'tenant_id' => tenant('id'),
            'tenant_name' => $tenant->name ?? null,
            'domain' => $tenant->domains->first()?->domain ?? null,
            'database' => $tenant->database()->getName() ?? null,
        ]);
    });

    // Example: Get tenant-specific data
    Route::get('/tenant/data', function () {
        // All database queries here automatically use tenant database
        return response()->json([
            'message' => 'This is tenant-specific data',
            'tenant_id' => tenant('id'),
            'current_database' => DB::connection()->getDatabaseName(),
        ]);
    });
});
