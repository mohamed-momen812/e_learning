<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenants\Http\Controllers\TenantController;

// Routes are loaded via RouteServiceProvider with:
// - 'api' prefix
// - 'super-admin' prefix
// - 'auth:sanctum' and 'super.admin' middleware

Route::get('tenants', [TenantController::class, 'index']);
Route::post('tenants', [TenantController::class, 'store']);
Route::post('tenants/bulk-delete', [TenantController::class, 'bulkDestroy']);
Route::get('tenants/{id}', [TenantController::class, 'show']);
Route::post('tenants/{id}', [TenantController::class, 'update']);
Route::delete('tenants/{id}', [TenantController::class, 'destroy']);
