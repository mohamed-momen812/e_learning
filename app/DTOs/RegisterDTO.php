<?php

namespace App\DTOs;

use Spatie\LaravelData\Data;

class RegisterDTO extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public ?string $phone,
        public string $password,
    ) {}
}
