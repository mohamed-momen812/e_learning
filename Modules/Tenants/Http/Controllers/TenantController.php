<?php

namespace Modules\Tenants\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use Modules\Tenants\Http\Resources\TenantResource;
use Modules\Tenants\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Modules\Tenants\Http\Requests\BulkDeleteTenantRequest;
use Modules\Tenants\Http\Requests\CreateTenantRequest;
use Modules\Tenants\Http\Requests\IndexTenantRequest;
use Modules\Tenants\Services\ListTenantService;
use Modules\Tenants\Services\TenantService;

class TenantController extends BaseApiController
{
    public function __construct(
        protected TenantService $service,
        protected ListTenantService $listService
    ) {}

    /**
     * Display a listing of tenants
     */
    public function index(IndexTenantRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Tenant::class);

        $defaultWith = ['domains'];
        $defaultFilters = [];
        $defaultSearch = '';
        $defaultSort = 'display_order';
        $defaultPerPage = 10;
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

        return $this->paginatedResponse($paginator, 'data.retrieved', TenantResource::class);
    }

    /**
     * Store a newly created tenant
     */
    public function store(CreateTenantRequest $request): JsonResponse
    {
        $this->authorize('create', Tenant::class);

        $tenant = $this->service->create($request->validated());

        return $this->createdResponse(
            new TenantResource($tenant->load('domains')),
            'tenant.created'
        );
    }

    /**
     * Display the specified tenant
     */
    public function show(string $id): JsonResponse
    {
        $tenant = $this->service->findOrFail($id);
        $this->authorize('view', $tenant);

        return $this->successResponse(
            new TenantResource($tenant->load('domains')),
            'data.retrieved'
        );
    }

    /**
     * Update the specified tenant
     */
    public function update(CreateTenantRequest $request, string $id): JsonResponse
    {
        $tenant = $this->service->findOrFail($id);
        $this->authorize('update', $tenant);

        $tenant = $this->service->update($id, $request->validated());

        return $this->successResponse(
            new TenantResource($tenant->load('domains')),
            'tenant.updated'
        );
    }

    /**
     * Remove the specified tenant
     */
    public function destroy(string $id): JsonResponse
    {
        $tenant = $this->service->findOrFail($id);
        $this->authorize('delete', $tenant);

        $this->service->delete($id);

        return $this->noContentResponse();
    }

    /**
     * Bulk delete tenants
     */
    public function bulkDestroy(BulkDeleteTenantRequest $request): JsonResponse
    {
        $this->authorize('bulkDelete', Tenant::class);

        $result = $this->service->bulkDelete($request->validated('ids'));

        $message = 'tenant.bulk_deleted';
        if ($result['skipped_count'] > 0) {
            $message = 'tenant.bulk_deleted_with_skipped';
        }

        return $this->successResponse($result, $message);
    }
}
