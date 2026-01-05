<?php

namespace Modules\Admin\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;
use Illuminate\Http\JsonResponse;
use Modules\Admin\Http\Requests\IndexPermissionRequest;
use Modules\Admin\Http\Requests\UpdatePermissionDisplayOrderRequest;
use Modules\Admin\Services\ListPermissionService;
use Modules\Admin\Services\PermissionService;
use Modules\Admin\Services\UpdateDisplayOrderService;

class PermissionController extends BaseApiController
{
    public function __construct(
        protected PermissionService $service,
        protected ListPermissionService $listService,
        protected UpdateDisplayOrderService $orderService
    ) {
        // Authorization is handled in each method
    }

    /**
     * Display a listing of permissions
     */
    public function index(IndexPermissionRequest $request): JsonResponse
    {
        $this->authorize('viewAny', Permission::class);
        $defaultFilters = [];
        $defaultSearch = '';
        $defaultSort = 'display_order';
        $defaultPerPage = 50;
        $defaultPage = 1;

        $params = [
            'filters' => $request->validated('filters', $defaultFilters),
            'search' => $request->validated('search', $defaultSearch),
            'sort' => $request->validated('sort', $defaultSort),
            'per_page' => $request->validated('per_page', $defaultPerPage),
            'page' => $request->validated('page', $defaultPage),
        ];

        $paginator = $this->listService->handle($params);

        return $this->paginatedResponse($paginator, 'data.retrieved', PermissionResource::class);
    }

    /**
     * Display the specified permission
     */
    public function show(string $id): JsonResponse
    {
        $permission = $this->service->findOrFail($id);
        $this->authorize('view', $permission);

        return $this->successResponse(
            new PermissionResource($permission),
            'data.retrieved'
        );
    }

    /**
     * Update display order for permissions
     */
    public function updateOrder(UpdatePermissionDisplayOrderRequest $request): JsonResponse
    {
        $this->authorize('updateOrder', Permission::class);

        if ($request->has('ids')) {
            $this->orderService->reorderPermissionsByIds($request->validated('ids'));
        } else {
            $this->orderService->updatePermissionOrder($request->validated('orders'));
        }

        return $this->successResponse(null, 'permissions.order_updated');
    }
}
