<?php

namespace Modules\Admin\DTOs;

class TenantDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $domain,
        public ?string $database,
        public string $created_at,
        public ?string $updated_at,
    ) {}

    public static function fromModel($tenant): self
    {
        return new self(
            id: $tenant->id,
            name: $tenant->name ?? '',
            domain: $tenant->domains->first()?->domain ?? '',
            database: $tenant->database()->getName(),  // Changed: use database() method
            created_at: $tenant->created_at?->toISOString() ?? '',
            updated_at: $tenant->updated_at?->toISOString(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'domain' => $this->domain,
            'database' => $this->database,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
