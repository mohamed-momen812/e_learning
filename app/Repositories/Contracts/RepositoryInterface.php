<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

interface RepositoryInterface
{
    /**
     * Create a new record
     */
    public function create(array $data): Model;

    /**
     * Update an existing record
     */
    public function update(Model $model, array $data): bool;

    /**
     * Delete a record
     */
    public function delete(Model $model): bool;

    /**
     * Find a record by ID
     */
    public function find(int $id): ?Model;

    /**
     * Find or fail
     */
    public function findOrFail(int $id): Model;

    /**
     * Get all records with pagination
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all records without pagination
     */
    public function all(array $filters = []): \Illuminate\Database\Eloquent\Collection;

    /**
     * Find by a specific field
     */
    public function findBy(string $field, mixed $value): ?Model;

    /**
     * Check if record exists
     */
    public function exists(int $id): bool;

    /**
     * Count records
     */
    public function count(array $filters = []): int;
}
