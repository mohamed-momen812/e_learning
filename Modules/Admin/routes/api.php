<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\TenantController;

Route::prefix('admin')->group(function () {
    Route::apiResource('tenants', TenantController::class)->names('admin.tenants');
});
