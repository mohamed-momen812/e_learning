<?php

use Illuminate\Support\Facades\Route;
use Modules\SuperAdmin\Http\Controllers\TenantController;

// Routes are loaded via RouteServiceProvider with:
// - 'api' prefix
// - 'super-admin' prefix
// - 'auth:sanctum' and 'super.admin' middleware


Route::get('tenants', [TenantController::class, 'index'])->name('superadmin.tenants.index');
Route::post('tenants', [TenantController::class, 'store'])->name('superadmin.tenants.store');
Route::get('tenants/{id}', [TenantController::class, 'show'])->name('superadmin.tenants.show');
Route::post('tenants/{id}', [TenantController::class, 'update'])->name('superadmin.tenants.update');
Route::delete('tenants/{id}', [TenantController::class, 'destroy'])->name('superadmin.tenants.destroy');
