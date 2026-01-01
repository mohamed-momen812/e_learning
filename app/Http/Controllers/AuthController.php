<?php

namespace App\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseApiController
{
    public function __construct(
        protected AuthService $authService
    ) {}

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register($request->validated());

        return $this->createdResponse([
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user' => $result['user'],
        ], 'auth.registered');
    }

    /**
     * Login user 
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return $this->successResponse([
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user' => $result['user'],
        ], 'auth.logged_in');
    }

    /**
     * Login admin (only teacher/assistant users can login here)
     */
    public function adminLogin(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->adminLogin($request->validated());

        return $this->successResponse([
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user' => $result['user'],
        ], 'auth.logged_in');
    }

    /**
     * Login super admin (only super admin users can login here)
     */
    public function superAdminLogin(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->superAdminLogin($request->validated());

        return $this->successResponse([
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user' => $result['user'],
        ], 'auth.logged_in');
    }

    /**
     * Logout user
     */
    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->successResponse(null, 'auth.logged_out');
    }

    /**
     * Get current authenticated user
     */
    public function me(): JsonResponse
    {
        $user = $this->authService->me();

        if (!$user) {
            return $this->errorResponse('auth.unauthenticated', [], 401);
        }

        return $this->successResponse($user, 'data.retrieved');
    }

    /**
     * Update current user profile
     */
    public function updateProfile(UpdateProfileRequest $request): JsonResponse
    {
        $user = $this->authService->me();

        if (!$user) {
            return $this->errorResponse('auth.unauthenticated', [], 401);
        }

        $user->update($request->validated());

        return $this->successResponse($user->fresh(), 'auth.profile_updated');
    }

    /**
     * Change password
     */
    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $this->authService->me();

        if (!$user) {
            return $this->errorResponse('auth.unauthenticated', [], 401);
        }

        if (!Hash::check($request->validated('current_password'), $user->password)) {
            return $this->errorResponse('auth.invalid_current_password', ['current_password' => ['auth.invalid_current_password']], 422);
        }

        $user->update([
            'password' => Hash::make($request->validated('password')),
        ]);

        return $this->successResponse(null, 'auth.password_changed');
    }
}
