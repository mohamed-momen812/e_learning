<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Core\Exceptions\BusinessException;

class AuthService
{
    /**
     * Authenticate user and return token
     */
    public function login(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new BusinessException(
                'auth.invalid_credentials',
                ['email' => ['auth.invalid_credentials']],
                401
            );
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        $expiresIn = config('sanctum.expiration', 1440) * 60;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
            'user' => $user,
        ];
    }

    /**
     * Authenticate admin user (teacher/assistant) and return token
     * Only allows users with teacher or assistant roles
     */
    public function adminLogin(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new BusinessException(
                'auth.invalid_credentials',
                ['email' => ['auth.invalid_credentials']],
                401
            );
        }

        // Only allow teacher or assistant users
        if (!$user->hasAnyRole(['teacher', 'assistant'])) {
            throw new BusinessException(
                'auth.unauthorized_admin_access',
                ['email' => ['auth.unauthorized_admin_access']],
                403
            );
        }

        $token = $user->createToken('admin-token')->plainTextToken;
        $expiresIn = config('sanctum.expiration', 1440) * 60;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
            'user' => $user,
        ];
    }

    /**
     * Authenticate super admin user and return token
     * Only allows users with is_super_admin = true
     */
    public function superAdminLogin(array $data): array
    {
        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new BusinessException(
                'auth.invalid_credentials',
                ['email' => ['auth.invalid_credentials']],
                401
            );
        }

        // Only allow super admin users
        if (!$user->isSuperAdmin()) {
            throw new BusinessException(
                'auth.unauthorized_super_admin_access',
                ['email' => ['auth.unauthorized_super_admin_access']],
                403
            );
        }

        $token = $user->createToken('super-admin-token')->plainTextToken;
        $expiresIn = config('sanctum.expiration', 1440) * 60;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
            'user' => $user,
        ];
    }

    /**
     * Register a new user
     */
    public function register(array $data): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
        ]);

        // Assign default role (student) only if we're in a tenant context
        // Roles only exist in tenant databases, not in central database
        if (tenancy()->initialized && method_exists($user, 'assignRole')) {
            $user->assignRole('student');
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        $expiresIn = config('sanctum.expiration', 1440) * 60;

        return [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $expiresIn,
            'user' => $user,
        ];
    }

    /**
     * Logout user (revoke current token)
     */
    public function logout(): void
    {
        $user = Auth::user();
        
        if ($user) {
            $user->currentAccessToken()?->delete();
        }
    }

    /**
     * Get current authenticated user
     */
    public function me(): ?User
    {
        return Auth::user();
    }
}
