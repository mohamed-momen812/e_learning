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
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Sanctum API authentication
        // $middleware->statefulApi();
        // $middleware->validateCsrfTokens(except: [
        //     'api/*',                // Exclude all routes starting with api/
        //     // 'stripe/webhook',       // Exclude a specific URI
        //     // 'http://example.com/foo/*', // Exclude by full URL
        // ]);

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
                'message' => __('auth.unauthorized'),
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

            // Translate message with attributes support
            $message = $e->getMessage();
            $translatedMessage = __($message);

            // If translation key exists and has placeholders, try to extract attributes from errors
            // This allows passing attributes like ['permission' => 'value'] for :permission placeholder
            if ($translatedMessage !== $message && ! empty($e->getErrors())) {
                // Extract attributes from errors array (first level keys can be used as attributes)
                $attributes = [];
                foreach ($e->getErrors() as $key => $value) {
                    if (is_string($key) && ! is_array($value)) {
                        $attributes[$key] = $value;
                    }
                }
                if (! empty($attributes)) {
                    $translatedMessage = __($message, $attributes);
                }
            }

            return response()->json([
                'success' => false,
                'message' => $translatedMessage,
                'errors' => $translatedErrors,
            ], $e->getStatusCode());
        });

        // Handle Laravel validation exceptions
        $exceptions->render(function (ValidationException $e) {
            // Get the errors and ensure they're properly formatted
            $errors = $e->errors();
            $translatedErrors = [];

            foreach ($errors as $field => $messages) {
                // Ensure messages is always an array
                $messageArray = is_array($messages) ? $messages : [$messages];

                $translatedErrors[$field] = array_map(function ($message) {
                    // Messages from Laravel validation are already translated with attributes replaced
                    // But we ensure they're properly formatted
                    return $message;
                }, $messageArray);
            }

            return response()->json([
                'success' => false,
                'message' => __('validation.failed'),
                'errors' => $translatedErrors,
            ], 422);
        });
    })->create();
