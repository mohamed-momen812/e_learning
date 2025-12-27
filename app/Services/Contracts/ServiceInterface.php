<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface ServiceInterface
{
    /**
     * Create a new resource
     */
    public function create($dto): Model;

    /**
     * Update an existing resource
     */
    public function update(int $id, $dto): Model;

    /**
     * Delete a resource
     */
    public function delete(int $id): bool;

    /**
     * Find a resource by ID
     */
    public function find(int $id): ?Model;

    /**
     * Find or fail
     */
    public function findOrFail(int $id): Model;

    /**
     * Get all resources with pagination
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
