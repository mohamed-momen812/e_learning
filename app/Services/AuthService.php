<?php

namespace App\Services;

use App\DTOs\AuthTokenDTO;
use App\DTOs\LoginDTO;
use App\DTOs\RegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    /**
     * Authenticate user and return token
     */
    public function login(LoginDTO $dto): AuthTokenDTO
    {
        $user = User::where('email', $dto->email)->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        $expiresIn = config('sanctum.expiration', 1440) * 60; // Convert minutes to seconds

        return new AuthTokenDTO(
            token: $token,
            token_type: 'Bearer',
            expires_in: $expiresIn,
            user: $user
        );
    }

    /**
     * Register a new user
     */
    public function register(RegisterDTO $dto): AuthTokenDTO
    {
        $user = User::create([
            'name' => $dto->name,
            'email' => $dto->email,
            'phone' => $dto->phone,
            'password' => Hash::make($dto->password),
        ]);

        // Assign default role (student) if roles exist
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('student');
        }

        $token = $user->createToken('auth-token')->plainTextToken;
        $expiresIn = config('sanctum.expiration', 1440) * 60; // Convert minutes to seconds

        return new AuthTokenDTO(
            token: $token,
            token_type: 'Bearer',
            expires_in: $expiresIn,
            user: $user
        );
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

