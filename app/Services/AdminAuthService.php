<?php

namespace App\Services;

use App\DTOs\AuthTokenDTO;
use App\DTOs\LoginDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminAuthService
{
    /**
     * Authenticate super admin and return token
     * Uses central database connection
     */
    public function login(LoginDTO $dto): AuthTokenDTO
    {
        // Query central database explicitly
        // User model uses central connection when on central domain
        $user = User::on('central')->where('email', $dto->email)->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('admin-auth-token')->plainTextToken;
        $expiresIn = config('sanctum.expiration', 1440) * 60; // Convert minutes to seconds

        return new AuthTokenDTO(
            token: $token,
            token_type: 'Bearer',
            expires_in: $expiresIn,
            user: $user
        );
    }

    /**
     * Logout super admin (revoke current token)
     */
    public function logout(): void
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if ($user) {
            $user->currentAccessToken()?->delete();
        }
    }

    /**
     * Get current authenticated super admin
     */
    public function me(): ?User
    {
        return Auth::user();
    }
}
