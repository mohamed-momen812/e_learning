<?php

use Illuminate\Support\Facades\Route;
use Modules\Admin\Http\Controllers\DirectPermissionController;
use Modules\Admin\Http\Controllers\PermissionController;
use Modules\Admin\Http\Controllers\RoleController;
use Modules\Admin\Http\Controllers\UserController;

// Routes are loaded via RouteServiceProvider with:
// - Tenant middleware (InitializeTenancyByHeader)
// - 'api' prefix
// - 'admin' prefix
// - 'auth:sanctum' and 'admin' middleware

Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
Route::post('users', [UserController::class, 'store'])->name('admin.users.store');
Route::post('users/bulk-delete', [UserController::class, 'bulkDestroy'])->name('admin.users.bulk_destroy');
Route::post('users/reorder', [UserController::class, 'updateOrder'])->name('admin.users.reorder');
Route::get('users/{id}', [UserController::class, 'show'])->name('admin.users.show');
Route::post('users/{id}', [UserController::class, 'update'])->name('admin.users.update');
Route::delete('users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

Route::get('users/{id}/direct-permissions', [DirectPermissionController::class, 'show'])->name('admin.users.direct_permissions.show');
Route::post('users/{id}/direct-permissions/assign', [DirectPermissionController::class, 'assign'])->name('admin.users.direct_permissions.assign');
Route::post('users/{id}/direct-permissions/revoke', [DirectPermissionController::class, 'revoke'])->name('admin.users.direct_permissions.revoke');

Route::get('roles', [RoleController::class, 'index'])->name('admin.roles.index');
Route::post('roles', [RoleController::class, 'store'])->name('admin.roles.store');
Route::post('roles/bulk-delete', [RoleController::class, 'bulkDestroy'])->name('admin.roles.bulk_destroy');
Route::post('roles/reorder', [RoleController::class, 'updateOrder'])->name('admin.roles.reorder');
Route::get('roles/{id}', [RoleController::class, 'show'])->name('admin.roles.show');
Route::post('roles/{id}', [RoleController::class, 'update'])->name('admin.roles.update');
Route::delete('roles/{id}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');

Route::get('permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
Route::get('permissions/{id}', [PermissionController::class, 'show'])->name('admin.permissions.show');
Route::post('permissions/reorder', [PermissionController::class, 'updateOrder'])->name('admin.permissions.reorder');
