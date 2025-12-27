<?php

namespace Modules\Admin\DTOs;

class CreateTenantDTO
{
    public function __construct(
        public string $name,
        public string $domain,
    ) {}

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'domain' => $this->domain,
        ];
    }

    public static function from(array $data): self
    {
        return new self(
            name: $data['name'],
            domain: $data['domain'],
        );
    }
}
