<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::prefix('admin/auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});

// Protected authentication routes (require authentication)
Route::prefix('admin/auth')->middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});