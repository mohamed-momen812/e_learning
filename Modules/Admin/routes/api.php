<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\UserController;
use Modules\Admin\Http\Controllers\RoleController;
use Modules\Admin\Http\Controllers\PermissionController;

// Routes are loaded via RouteServiceProvider with:
// - Tenant middleware (InitializeTenancyBySubdomain, PreventAccessFromCentralDomains)
// - 'api' prefix
// - 'admin' prefix
// - 'auth:sanctum' middleware

Route::apiResource('users', UserController::class)->names('admin.users');

Route::apiResource('roles', RoleController::class)->names('admin.roles');

// Permission management (read-only)
Route::get('permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
Route::get('permissions/{id}', [PermissionController::class, 'show'])->name('admin.permissions.show');
