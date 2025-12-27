<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array following JSON:API specification.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type' => 'users',
            'id' => (string) $this->id,
            'attributes' => [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'email_verified_at' => $this->email_verified_at?->toIso8601String(),
                'created_at' => $this->created_at->toIso8601String(),
                'updated_at' => $this->updated_at->toIso8601String(),
            ],
            'relationships' => [
                'roles' => [
                    'data' => $this->whenLoaded('roles', function () {
                        return $this->roles->map(function ($role) {
                            return [
                                'type' => 'roles',
                                'id' => (string) $role->id,
                            ];
                        });
                    }),
                ],
            ],
        ];
    }
}

