<?php

namespace App\Http\Controllers;

use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $dto = RegisterDTO::from($request->validated());
        $authToken = $this->authService->register($dto);

        return response()->json([
            'data' => [
                'type' => 'auth-tokens',
                'attributes' => [
                    'token' => $authToken->token,
                    'token_type' => $authToken->token_type,
                    'expires_in' => $authToken->expires_in,
                ],
                'relationships' => [
                    'user' => [
                        'data' => new UserResource($authToken->user),
                    ],
                ],
            ],
        ], 201);
    }

    /**
     * Login user
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginDTO::from($request->validated());
        $authToken = $this->authService->login($dto);

        return response()->json([
            'data' => [
                'type' => 'auth-tokens',
                'attributes' => [
                    'token' => $authToken->token,
                    'token_type' => $authToken->token_type,
                    'expires_in' => $authToken->expires_in,
                ],
                'relationships' => [
                    'user' => [
                        'data' => new UserResource($authToken->user),
                    ],
                ],
            ],
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout();

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->me();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated',
            ], 401);
        }

        return response()->json([
            'data' => new UserResource($user),
        ]);
    }
}
