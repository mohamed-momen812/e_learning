<?php

namespace Modules\Admin\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TenantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'type' => 'tenant',
            'attributes' => [
                'name' => $this->name ?? '',
                'domain' => $this->domains->first()?->domain ?? '',
                'database' => $this->database()->getName(),
                'created_at' => $this->created_at?->toISOString(),
                'updated_at' => $this->updated_at?->toISOString(),
            ],
        ];
    }
}
