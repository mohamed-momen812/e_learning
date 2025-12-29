<?php

namespace Modules\SuperAdmin\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use Modules\SuperAdmin\Http\Requests\CreateTenantRequest;
use Modules\SuperAdmin\Http\Requests\IndexTenantRequest;
use Modules\SuperAdmin\Services\TenantService;
use Modules\SuperAdmin\Services\ListTenantService;
use Illuminate\Http\JsonResponse;

class TenantController extends BaseApiController
{
    public function __construct(
        protected TenantService $service,
        protected ListTenantService $listService
    ) {
        // TODO: Add authorization middleware for super admin
    }

    /**
     * Display a listing of tenants
     */
    public function index(IndexTenantRequest $request): JsonResponse
    {
        $defaultWith = ['domains'];
        $defaultFilters = [];
        $defaultSearch = '';
        $defaultSort = 'created_at';
        $defaultPerPage = 15;
        $defaultPage = 1;

        $params = [
            'with' => $request->validated('with', $defaultWith),
            'filters' => $request->validated('filters', $defaultFilters),
            'search' => $request->validated('search', $defaultSearch),
            'sort' => $request->validated('sort', $defaultSort),
            'per_page' => $request->validated('per_page', $defaultPerPage),
            'page' => $request->validated('page', $defaultPage),
        ];

        $paginator = $this->listService->handle($params);

        return $this->paginatedResponse($paginator, 'data.retrieved');
    }

    /**
     * Store a newly created tenant
     */
    public function store(CreateTenantRequest $request): JsonResponse
    {
        $tenant = $this->service->create($request->validated());

        return $this->createdResponse($tenant, 'tenant.created');
    }

    /**
     * Display the specified tenant
     */
    public function show(string $id): JsonResponse
    {
        $tenant = $this->service->findOrFail($id);

        return $this->successResponse($tenant, 'data.retrieved');
    }

    /**
     * Update the specified tenant
     */
    public function update(CreateTenantRequest $request, string $id): JsonResponse
    {
        $tenant = $this->service->update($id, $request->validated());

        return $this->successResponse($tenant, 'tenant.updated');
    }

    /**
     * Remove the specified tenant
     */
    public function destroy(string $id): JsonResponse
    {
        $this->service->delete($id);

        return $this->noContentResponse();
    }
}

