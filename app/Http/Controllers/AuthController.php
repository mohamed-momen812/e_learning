<?php

namespace App\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends BaseApiController
{
    public function __construct(
        protected AuthService $authService,
        protected ImageService $imageService
    ) {}

    /**
     * Register a new user
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $avatar = $request->file('avatar');

        // Remove avatar from data array as it's handled separately
        unset($data['avatar']);

        $result = $this->authService->register($data, $avatar);

        return $this->createdResponse([
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user' => new UserResource($result['user']->load('avatar')),
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
            'user' => new UserResource($result['user']->load('avatar')),
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
            'user' => new UserResource($result['user']->load(['avatar', 'roles'])),
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
            'user' => new UserResource($result['user']->load('avatar')),
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

        return $this->successResponse(
            new UserResource($user->load('avatar', 'roles')),
            'data.retrieved'
        );
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

        $data = $request->validated();

        if ($request->hasFile('avatar')) {
            $this->imageService->uploadAndAttach($user, $request->file('avatar'), 'avatar');
            unset($data['avatar']); // Remove from data array as it's not a user field
        }

        if (!empty($data)) {
            $user->update($data);
        }

        return $this->successResponse(
            new UserResource($user->load('avatar', 'roles')),
            'auth.profile_updated'
        );
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
