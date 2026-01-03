<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        // Super Admin routes
        Route::prefix('super-admin')->group(function () {
            Route::prefix('auth')->group(function () {
                Route::post('/login', [AuthController::class, 'superAdminLogin']);

                Route::middleware(['auth:sanctum', 'super.admin'])->group(function () {
                    Route::post('/logout', [AuthController::class, 'logout']);
                    Route::get('/me', [AuthController::class, 'me']);
                    Route::post('/profile', [AuthController::class, 'updateProfile']);
                    Route::post('/change-password', [AuthController::class, 'changePassword']);
                });
            });
        });

        // Student routes - accessible
        Route::prefix('auth')->group(function () {
            Route::post('/register', [AuthController::class, 'register']);
            Route::post('/login', [AuthController::class, 'login']);
        });

        Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::get('/me', [AuthController::class, 'me']);
            Route::post('/profile', [AuthController::class, 'updateProfile']);
            Route::post('/change-password', [AuthController::class, 'changePassword']);
        });
    });
}
