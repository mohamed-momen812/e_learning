<?php

namespace App\Core\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseApiController extends Controller
{
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
            $response['errors'] = $errors;
        }
        
        return response()->json($response, $statusCode);
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
     */
    protected function paginatedResponse(LengthAwarePaginator $paginator, ?string $message = null): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $paginator->items(),
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

