<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class AuthTokenDTO extends Data
{
    public function __construct(
        public string $token,
        public string $token_type,
        public int $expires_in,
        public object $user,
    ) {}
}
