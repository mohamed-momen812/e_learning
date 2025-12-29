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

        // Assign default role (student) if roles exist
        if (method_exists($user, 'assignRole')) {
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
