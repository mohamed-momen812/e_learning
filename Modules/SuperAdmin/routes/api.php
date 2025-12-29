<?php

use Illuminate\Support\Facades\Route;
use Modules\SuperAdmin\Http\Controllers\TenantController;

// Routes are loaded via RouteServiceProvider with:
// - Central domain restriction (only accessible from central domains)
// - 'super-admin' prefix
// - 'auth:sanctum' middleware

Route::apiResource('tenants', TenantController::class)->names('superadmin.tenants');
