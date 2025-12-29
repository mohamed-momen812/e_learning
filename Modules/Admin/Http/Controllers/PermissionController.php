<?php

namespace Modules\Admin\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use Modules\Admin\Services\PermissionService;
use Modules\Admin\Services\ListPermissionService;
use Modules\Admin\Http\Requests\IndexPermissionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class PermissionController extends BaseApiController
{
    public function __construct(
        protected PermissionService $service,
        protected ListPermissionService $listService
    ) {
        // Authorization is handled in each method
    }

    /**
     * Display a listing of permissions
     */
    public function index(IndexPermissionRequest $request): JsonResponse
    {
        $this->authorizePermissionAccess();
        $defaultFilters = [];
        $defaultSearch = '';
        $defaultSort = 'name';
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

        return $this->paginatedResponse($paginator, 'data.retrieved');
    }

    /**
     * Display the specified permission
     */
    public function show(string $id): JsonResponse
    {
        $this->authorizePermissionAccess();
        $permission = $this->service->findOrFail($id);

        return $this->successResponse($permission, 'data.retrieved');
    }

    /**
     * Authorize permission access - only teachers can view permissions
     */
    protected function authorizePermissionAccess(): void
    {
        if (!Auth::user()?->hasRole('teacher')) {
            abort(403, 'Only teachers can view permissions');
        }
    }
}

