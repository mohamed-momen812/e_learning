<?php

namespace App\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use App\Http\Resources\DirectPermissionResource;
use App\Models\User;
use App\Services\DirectPermissionService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\AssignDirectPermissionRequest;
use App\Http\Requests\RevokeDirectPermissionRequest;

class DirectPermissionController extends BaseApiController
{
    public function __construct(
        protected DirectPermissionService $service
    ) {
        // Authorization is handled via policies in each method
    }

    /**
     * Assign direct permissions to a user.
     */
    public function assign(AssignDirectPermissionRequest $request, string $userId): JsonResponse
    {
        $this->authorize('managePermissions', User::class);

        $user = User::findOrFail($userId);

        $validated = $request->validated();

        $this->service->assign(
            user: $user,
            permissions: $validated['permissions'],
        );

        // Refresh user to get updated permissions
        $user->refresh();
        $directPermissions = $this->service->getDirectPermissions($user);

        return $this->successResponse(
            DirectPermissionResource::collection($directPermissions),
            'permissions.assigned_successfully'
        );
    }

    /**
     * Revoke direct permissions from a user.
     */
    public function revoke(RevokeDirectPermissionRequest $request, string $userId): JsonResponse
    {
        $this->authorize('managePermissions', User::class);

        $user = User::findOrFail($userId);

        $validated = $request->validated();

        $this->service->revoke(
            user: $user,
            permissions: $validated['permissions'],
        );

        // Refresh user to get updated permissions
        $user->refresh();
        $directPermissions = $this->service->getDirectPermissions($user);

        return $this->successResponse(
            DirectPermissionResource::collection($directPermissions),
            'permissions.revoked_successfully'
        );
    }

    /**
     * Get all direct permissions for a user.
     */
    public function show(string $userId): JsonResponse
    {
        $this->authorize('managePermissions', User::class);

        $user = User::findOrFail($userId);
        $directPermissions = $this->service->getDirectPermissions($user);

        return $this->successResponse(
            DirectPermissionResource::collection($directPermissions),
            'permissions.retrieved_successfully'
        );
    }
}
