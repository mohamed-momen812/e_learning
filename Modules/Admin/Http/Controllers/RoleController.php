<?php

namespace Modules\Admin\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use Modules\Admin\Http\Requests\CreateRoleRequest;
use Modules\Admin\Http\Requests\UpdateRoleRequest;
use Modules\Admin\Http\Requests\IndexRoleRequest;
use Modules\Admin\Services\RoleService;
use Modules\Admin\Services\ListRoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class RoleController extends BaseApiController
{
    public function __construct(
        protected RoleService $service,
        protected ListRoleService $listService
    ) {
        // Authorization is handled in each method
    }

    /**
     * Display a listing of roles
     */
    public function index(IndexRoleRequest $request): JsonResponse
    {
        $this->authorizeRoleAccess();
        $defaultWith = ['permissions'];
        $defaultFilters = [];
        $defaultSearch = '';
        $defaultSort = 'name';
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
     * Store a newly created role
     */
    public function store(CreateRoleRequest $request): JsonResponse
    {
        $this->authorizeRoleAccess();
        $role = $this->service->createRole(
            $request->validated('name'),
            $request->validated('permissions', [])
        );

        return $this->createdResponse($role, 'role.created');
    }

    /**
     * Display the specified role
     */
    public function show(string $id): JsonResponse
    {
        $this->authorizeRoleAccess();
        $role = $this->service->findOrFail($id);

        return $this->successResponse($role, 'data.retrieved');
    }

    /**
     * Update the specified role
     */
    public function update(UpdateRoleRequest $request, string $id): JsonResponse
    {
        $this->authorizeRoleAccess();
        $role = $this->service->updateRole(
            $id,
            $request->validated('name'),
            $request->validated('permissions', [])
        );

        return $this->successResponse($role, 'role.updated');
    }

    /**
     * Remove the specified role
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorizeRoleAccess();
        $this->service->deleteRole($id);

        return $this->noContentResponse();
    }

    /**
     * Authorize role access - only teachers can manage roles
     */
    protected function authorizeRoleAccess(): void
    {
        if (!Auth::user()?->hasRole('teacher')) {
            abort(403, 'Only teachers can manage roles');
        }
    }
}
