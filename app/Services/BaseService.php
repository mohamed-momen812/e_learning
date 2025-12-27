<?php

namespace App\Services;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseService
{
    protected RepositoryInterface $repository;

    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create a new resource
     */
    public function create($dto): Model
    {
        return DB::transaction(function () use ($dto) {
            $model = $this->repository->create($dto->toArray());

            // Dispatch event if needed
            // event(new ResourceCreated($model));

            return $model;
        });
    }

    /**
     * Update an existing resource
     */
    public function update(int $id, $dto): Model
    {
        return DB::transaction(function () use ($id, $dto) {
            $model = $this->repository->findOrFail($id);
            $this->repository->update($model, $dto->toArray());

            // Dispatch event if needed
            // event(new ResourceUpdated($model));

            return $model->fresh();
        });
    }

    /**
     * Delete a resource
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $model = $this->repository->findOrFail($id);
            $deleted = $this->repository->delete($model);

            // Dispatch event if needed
            // event(new ResourceDeleted($model));

            return $deleted;
        });
    }

    /**
     * Find a resource by ID
     */
    public function find(int $id): ?Model
    {
        return $this->repository->find($id);
    }

    /**
     * Find or fail
     */
    public function findOrFail(int $id): Model
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Get all resources with pagination
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->repository->getAll($filters, $perPage);
    }

    /**
     * Get all resources without pagination
     */
    public function all(array $filters = []): \Illuminate\Database\Eloquent\Collection
    {
        return $this->repository->all($filters);
    }

    /**
     * Find by a specific field
     */
    public function findBy(string $field, mixed $value): ?Model
    {
        return $this->repository->findBy($field, $value);
    }

    /**
     * Check if resource exists
     */
    public function exists(int $id): bool
    {
        return $this->repository->exists($id);
    }

    /**
     * Count resources
     */
    public function count(array $filters = []): int
    {
        return $this->repository->count($filters);
    }
}
