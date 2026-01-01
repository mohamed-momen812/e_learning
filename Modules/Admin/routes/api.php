<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\UserController;
use Modules\Admin\Http\Controllers\RoleController;
use Modules\Admin\Http\Controllers\PermissionController;

// Routes are loaded via RouteServiceProvider with:
// - Tenant middleware (InitializeTenancyByHeader)
// - 'api' prefix
// - 'admin' prefix
// - 'auth:sanctum' and 'admin' middleware

Route::apiResource('users', UserController::class)->names('admin.users');
Route::post('users/reorder', [UserController::class, 'updateOrder'])->name('admin.users.reorder');

Route::apiResource('roles', RoleController::class)->names('admin.roles');
Route::post('roles/reorder', [RoleController::class, 'updateOrder'])->name('admin.roles.reorder');

// Permission management (read-only)
Route::get('permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
Route::get('permissions/{id}', [PermissionController::class, 'show'])->name('admin.permissions.show');
Route::post('permissions/reorder', [PermissionController::class, 'updateOrder'])->name('admin.permissions.reorder');
