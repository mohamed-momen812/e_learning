<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::prefix('super-admin')->group(function () {
            Route::prefix('auth')->group(function () {
                Route::post('/login', [AuthController::class, 'login']);

                Route::middleware('auth:sanctum')->group(function () {
                    Route::post('/logout', [AuthController::class, 'logout']);
                    Route::get('/me', [AuthController::class, 'me']);
                    Route::put('/profile', [AuthController::class, 'updateProfile']);
                    Route::post('/change-password', [AuthController::class, 'changePassword']);
                });
            });
        });
    });
}
