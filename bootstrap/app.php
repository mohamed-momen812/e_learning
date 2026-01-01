<?php

use App\Core\Exceptions\BaseException;
use App\Core\Exceptions\ValidationException;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
    web: __DIR__ . '/../routes/web.php',
    api: __DIR__ . '/../routes/api.php',
    commands: __DIR__ . '/../routes/console.php',
    health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    // Sanctum API authentication
    $middleware->statefulApi();

    // Set locale based on Accept-Language header
    $middleware->append(SetLocale::class);

    // Register middleware aliases
    $middleware->alias([
        'super.admin' => EnsureSuperAdmin::class,
        'admin' => EnsureAdmin::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
    // Handle route not found exceptions (404 Not Found)
    $exceptions->render(function (NotFoundHttpException $e) {
        return response()->json([
            'success' => false,
            'message' => __('data.route_not_found'),
        ], 404);
    });

    // Handle authorization exceptions (403 Forbidden)
    $exceptions->render(function (AccessDeniedHttpException $e) {
        return response()->json([
            'success' => false,
            'message' =>  __('auth.unauthorized'),
        ], 403);
    });

    // Handle Core exceptions
    $exceptions->render(function (BaseException $e) {
        // Translate all error messages in the errors array
        $translatedErrors = [];
        foreach ($e->getErrors() as $field => $messages) {
            // Ensure messages is always an array
            $messageArray = is_array($messages) ? $messages : [$messages];

            $translatedErrors[$field] = array_map(function ($message) {
                // If message looks like a translation key (e.g., "auth.invalid_credentials"),
                // translate it
                if (is_string($message) && preg_match('/^[a-z_]+\.[a-z_]+(\.[a-z_]+)*$/i', $message)) {
                    $translated = __($message);
                    // Only use translation if it's different from the key (translation exists)
                    return $translated !== $message ? $translated : $message;
                }
                // Already translated or not a translation key, return as-is
                return $message;
            }, $messageArray);
        }

        return response()->json([
            'success' => false,
            'message' => __($e->getMessage()),
            'errors' => $translatedErrors,
        ], $e->getStatusCode());
    });

    // Handle Laravel validation exceptions
    $exceptions->render(function (ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => __('validation.failed'),
            'errors' => $e->getErrors(),
        ], 422);
    });
    })->create();
