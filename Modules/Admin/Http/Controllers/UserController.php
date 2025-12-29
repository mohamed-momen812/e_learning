<?php

namespace Modules\Admin\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use App\Models\User;
use Modules\Admin\Http\Requests\CreateUserRequest;
use Modules\Admin\Http\Requests\UpdateUserRequest;
use Modules\Admin\Http\Requests\IndexUserRequest;
use Modules\Admin\Services\UserService;
use Modules\Admin\Services\ListUserService;
use Illuminate\Http\JsonResponse;

class UserController extends BaseApiController
{
    public function __construct(
        protected UserService $service,
        protected ListUserService $listService
    ) {
        // Authorization is handled via policies in each method
    }

    /**
     * Display a listing of users
     */
    public function index(IndexUserRequest $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);
        $defaultWith = ['roles'];
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
     * Store a newly created user
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);
        $user = $this->service->create($request->validated());

        return $this->createdResponse($user, 'user.created');
    }

    /**
     * Display the specified user
     */
    public function show(string $id): JsonResponse
    {
        $user = $this->service->findOrFail($id);
        $this->authorize('view', $user);

        return $this->successResponse($user, 'data.retrieved');
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, string $id): JsonResponse
    {
        $user = $this->service->findOrFail($id);
        $this->authorize('update', $user);
        $user = $this->service->update($id, $request->validated());

        return $this->successResponse($user, 'user.updated');
    }

    /**
     * Remove the specified user
     */
    public function destroy(string $id): JsonResponse
    {
        $user = $this->service->findOrFail($id);
        $this->authorize('delete', $user);
        $this->service->delete($id);

        return $this->noContentResponse();
    }
}
