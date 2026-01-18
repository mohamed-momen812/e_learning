<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DirectPermissionController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SelectOptionsController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\InitializeTenancyByHeader;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| These routes are accessible via tenant subdomain (e.g., test.e_learning.test)
| The middleware automatically initializes tenant context.
|
*/

Route::middleware([
    'api',
    InitializeTenancyByHeader::class,
])->prefix('api')->group(function () {
    // Public helper routes for select options
    Route::prefix('helpers/tenant')->group(function () {
        Route::get('/select-options', [SelectOptionsController::class, 'index']);
        Route::get('/select-options/roles', [SelectOptionsController::class, 'roles']);
        Route::get('/select-options/permissions', [SelectOptionsController::class, 'permissions']);
        Route::get('/select-options/users', [SelectOptionsController::class, 'users']);
    });

    // Admin routes - for admin and assistant dashboard
    Route::prefix('admin')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/login', [AuthController::class, 'adminLogin']);
        });

        Route::middleware(['auth:sanctum', 'admin'])->group(function () {
            Route::prefix('auth')->group(function () {
                Route::post('/logout', [AuthController::class, 'logout']);
                Route::get('/me', [AuthController::class, 'me']);
                Route::post('/profile', [AuthController::class, 'updateProfile']);
                Route::post('/change-password', [AuthController::class, 'changePassword']);
            });

            Route::prefix('users')->group(function () {
                Route::get('/', [UserController::class, 'index']);
                Route::post('/', [UserController::class, 'store']);
                Route::post('/bulk-delete', [UserController::class, 'bulkDestroy']);
                Route::post('/reorder', [UserController::class, 'updateOrder']);
                Route::get('/{id}', [UserController::class, 'show']);
                Route::post('/{id}', [UserController::class, 'update']);
                Route::delete('/{id}', [UserController::class, 'destroy']);
                Route::post('/{id}/direct-permissions/assign', [DirectPermissionController::class, 'assign']);
                Route::post('/{id}/direct-permissions/revoke', [DirectPermissionController::class, 'revoke']);
                Route::get('/{id}/direct-permissions', [DirectPermissionController::class, 'show']);
            });


            Route::prefix('roles')->group(function () {
                Route::get('/', [RoleController::class, 'index']);
                Route::post('/', [RoleController::class, 'store']);
                Route::post('/bulk-delete', [RoleController::class, 'bulkDestroy']);
                Route::post('/reorder', [RoleController::class, 'updateOrder']);
                Route::get('/{id}', [RoleController::class, 'show']);
                Route::post('/{id}', [RoleController::class, 'update']);
                Route::delete('/{id}', [RoleController::class, 'destroy']);
            });

            Route::prefix('permissions')->group(function () {
                Route::get('/', [PermissionController::class, 'index']);
                Route::get('/{id}', [PermissionController::class, 'show']);
                Route::post('/reorder', [PermissionController::class, 'updateOrder']);
            });
        });
    });

    // Auth routes for students (tenant context)
    Route::prefix('tenant')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
        });

        // Auth routes for students (protected)
        Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
    });
});
