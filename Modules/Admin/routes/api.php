<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\TenantController;

Route::prefix('admin')->group(function () {
    // Protected super admin routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('tenants', TenantController::class)->names('admin.tenants');
    });
});
