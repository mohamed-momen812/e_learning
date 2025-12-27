<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Admin\DTOs\CreateTenantDTO;
use Modules\Admin\Http\Requests\CreateTenantRequest;
use Modules\Admin\Http\Resources\TenantResource;
use Modules\Admin\Services\Contracts\TenantServiceInterface;

class TenantController extends Controller
{
    public function __construct(
        protected TenantServiceInterface $service
    ) {
        // TODO: Add authorization middleware for super admin
    }

    /**
     * Display a listing of tenants
     */
    public function index(): AnonymousResourceCollection
    {
        $tenants = $this->service->getAll(request()->all());
        return TenantResource::collection($tenants);
    }

    /**
     * Store a newly created tenant
     */
    public function store(CreateTenantRequest $request): JsonResponse
    {
        $dto = CreateTenantDTO::from($request->validated());
        $tenantDTO = $this->service->create($dto);

        // Fetch the tenant model to return as resource
        $tenant = $this->service->findOrFail($tenantDTO->id);

        return (new TenantResource($tenant))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified tenant
     */
    public function show(string $id): TenantResource
    {
        $tenant = $this->service->findOrFail($id);
        return new TenantResource($tenant);
    }

    /**
     * Update the specified tenant
     */
    public function update(CreateTenantRequest $request, string $id): TenantResource
    {
        $dto = CreateTenantDTO::from($request->validated());
        $tenantDTO = $this->service->update($id, $dto);

        // Fetch the tenant model to return as resource
        $tenant = $this->service->findOrFail($tenantDTO->id);

        return new TenantResource($tenant);
    }

    /**
     * Remove the specified tenant
     */
    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);
        return response()->json(null, 204);
    }
}
