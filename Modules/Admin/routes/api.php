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

Route::get('users', [UserController::class, 'index'])->name('admin.users.index');
Route::get('users/{id}', [UserController::class, 'show'])->name('admin.users.show');
Route::post('users', [UserController::class, 'store'])->name('admin.users.store');
Route::post('users/{id}', [UserController::class, 'update'])->name('admin.users.update');
Route::delete('users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');
Route::post('reorder/users', [UserController::class, 'updateOrder'])->name('admin.users.reorder');

Route::get('roles', [RoleController::class, 'index'])->name('admin.roles.index');
Route::get('roles/{id}', [RoleController::class, 'show'])->name('admin.roles.show');
Route::post('roles', [RoleController::class, 'store'])->name('admin.roles.store');
Route::post('roles/{id}', [RoleController::class, 'update'])->name('admin.roles.update');
Route::delete('roles/{id}', [RoleController::class, 'destroy'])->name('admin.roles.destroy');
Route::post('reorder/roles', [RoleController::class, 'updateOrder'])->name('admin.roles.reorder');

// Permission management (read-only)
Route::get('permissions', [PermissionController::class, 'index'])->name('admin.permissions.index');
Route::get('permissions/{id}', [PermissionController::class, 'show'])->name('admin.permissions.show');
Route::post('reorder/permissions', [PermissionController::class, 'updateOrder'])->name('admin.permissions.reorder');
