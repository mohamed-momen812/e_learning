<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Middleware\InitializeTenancyByHeader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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
    // Admin routes - for teacher and assistant dashboard
    Route::prefix('admin')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('/login', [AuthController::class, 'adminLogin']);
        });

        Route::prefix('auth')->middleware(['auth:sanctum', 'admin'])->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
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
            Route::put('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
    });
});
