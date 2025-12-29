<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    // Remove subdomain middleware from global API routes
    // Admin routes should be accessible from central domain
    // Subdomain middleware will be applied to tenant-specific routes only

    // Sanctum API authentication
    $middleware->statefulApi();

    // Set locale based on Accept-Language header
    $middleware->append(\App\Http\Middleware\SetLocale::class);
})
    ->withExceptions(function (Exceptions $exceptions): void {
    // Handle Core exceptions
    $exceptions->render(function (App\Core\Exceptions\BaseException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => __($e->getMessage()),
            'errors' => $e->getErrors(),
        ], $e->getStatusCode());
    });

    // Handle Laravel validation exceptions
    $exceptions->render(function (Illuminate\Validation\ValidationException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => __('validation.failed'),
            'errors' => $e->errors(),
        ], 422);
    });
    })->create();
