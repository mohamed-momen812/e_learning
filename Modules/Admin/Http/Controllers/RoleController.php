<?php

namespace Modules\Admin\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use App\Http\Resources\RoleResource;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Modules\Admin\Http\Requests\BulkDeleteRoleRequest;
use Modules\Admin\Http\Requests\CreateRoleRequest;
use Modules\Admin\Http\Requests\IndexRoleRequest;
use Modules\Admin\Http\Requests\UpdateRoleDisplayOrderRequest;
use Modules\Admin\Http\Requests\UpdateRoleRequest;
use Modules\Admin\Services\ListRoleService;
use Modules\Admin\Services\RoleService;
use Modules\Admin\Services\UpdateDisplayOrderService;

class RoleController extends BaseApiController
{
    public function __construct(
        protected RoleService $service,
        protected ListRoleService $listService,
        protected UpdateDisplayOrderService $orderService
    ) {
        // Authorization is handled in each method
    }

    /**
     * Display a listing of roles
     */
    public function index(IndexRoleRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Role::class);
        $defaultWith = ['permissions'];
        $defaultFilters = [];
        $defaultSearch = '';
        $defaultSort = 'display_order';
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

        return $this->paginatedResponse($paginator, 'data.retrieved', RoleResource::class);
    }

    /**
     * Store a newly created role
     */
    public function store(CreateRoleRequest $request): JsonResponse
    {
        $this->authorize('create', Role::class);
        $role = $this->service->createRole(
            $request->validated('name'),
            $request->validated('permissions', []),
            $request->validated('label')
        );

        return $this->createdResponse(
            new RoleResource($role->load('permissions')),
            'role.created'
        );
    }

    /**
     * Display the specified role
     */
    public function show(string $id): JsonResponse
    {
        $role = $this->service->findOrFail($id);
        $this->authorize('view', $role);

        return $this->successResponse(
            new RoleResource($role->load('permissions')),
            'data.retrieved'
        );
    }

    /**
     * Update the specified role
     */
    public function update(UpdateRoleRequest $request, string $id): JsonResponse
    {
        $role = $this->service->findOrFail($id);
        $this->authorize('update', $role);
        $role = $this->service->updateRole(
            $id,
            $request->validated('name'),
            $request->validated('permissions', []),
            $request->validated('label')
        );

        return $this->successResponse(
            new RoleResource($role->load('permissions')),
            'role.updated'
        );
    }

    /**
     * Remove the specified role
     */
    public function destroy(string $id): JsonResponse
    {
        $role = $this->service->findOrFail($id);
        $this->authorize('delete', $role);
        $this->service->deleteRole($id);

        return $this->noContentResponse();
    }

    /**
     * Bulk delete roles
     */
    public function bulkDestroy(BulkDeleteRoleRequest $request): JsonResponse
    {
        $this->authorize('bulkDelete', Role::class);

        $result = $this->service->bulkDeleteRoles($request->validated('ids'));

        $message = 'role.bulk_deleted';
        if ($result['skipped_count'] > 0) {
            $message = 'role.bulk_deleted_with_skipped';
        }

        return $this->successResponse($result, $message);
    }

    /**
     * Update display order for roles
     */
    public function updateOrder(UpdateRoleDisplayOrderRequest $request): JsonResponse
    {
        $this->authorize('updateOrder', Role::class);

        if ($request->has('ids')) {
            $this->orderService->reorderRolesByIds($request->validated('ids'));
        } else {
            $this->orderService->updateRoleOrder($request->validated('orders'));
        }

        return $this->successResponse(null, 'role.order_updated');
    }
}
