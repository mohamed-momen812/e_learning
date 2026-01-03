<?php

namespace App\Core\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseApiController extends Controller
{
    use AuthorizesRequests;

    /**
     * Return success response
     */
    protected function successResponse($data, ?string $message = null, int $statusCode = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $data,
        ];
        
        if ($message) {
            $response['message'] = __($message);
        }
        
        return response()->json($response, $statusCode);
    }
    
    /**
     * Return error response
     */
    protected function errorResponse(string $message, array $errors = [], int $statusCode = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => __($message),
        ];
        
        if (!empty($errors)) {
            $response['errors'] = $this->translateErrors($errors);
        }
        
        return response()->json($response, $statusCode);
    }

    /**
     * Translate error messages recursively
     * 
     * @param array $errors
     * @return array
     */
    protected function translateErrors(array $errors): array
    {
        $translatedErrors = [];

        foreach ($errors as $field => $messages) {
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

        return $translatedErrors;
    }
    
    /**
     * Return created response
     */
    protected function createdResponse($data, ?string $message = null): JsonResponse
    {
        return $this->successResponse($data, $message, 201);
    }
    
    /**
     * Return no content response
     */
    protected function noContentResponse(): JsonResponse
    {
        return response()->json(null, 204);
    }
    
    /**
     * Return paginated response
     * Supports both raw models and resources
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, ?string $message = null, ?string $resourceClass = null): JsonResponse
    {
        $items = $paginator->items();
        
        // Transform items using resource if provided
        if ($resourceClass && class_exists($resourceClass)) {
            $items = $resourceClass::collection($items);
        }
        
        $response = [
            'success' => true,
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ];
        
        if ($message) {
            $response['message'] = __($message);
        }
        
        return response()->json($response, 200);
    }
}

