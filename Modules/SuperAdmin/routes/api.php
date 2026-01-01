<?php

use Illuminate\Support\Facades\Route;
use Modules\SuperAdmin\Http\Controllers\TenantController;

// Routes are loaded via RouteServiceProvider with:
// - 'api' prefix
// - 'super-admin' prefix
// - 'auth:sanctum' and 'super.admin' middleware


Route::apiResource('tenants', TenantController::class)->names('superadmin.tenants');
