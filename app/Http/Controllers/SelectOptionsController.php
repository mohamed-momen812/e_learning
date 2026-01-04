<?php

namespace App\Http\Controllers;

use App\Core\Controllers\BaseApiController;
use App\Services\SelectOptionsService;
use Illuminate\Http\JsonResponse;

class SelectOptionsController extends BaseApiController
{
    public function __construct(
        protected SelectOptionsService $service
    ) {}

    /**
     * Get all available select options in one request
     * Useful for frontend to fetch all dropdown data at once
     */
    public function index(): JsonResponse
    {
        return $this->successResponse(
            $this->service->getAll(),
            'select_options.retrieved'
        );
    }

    /**
     * Get roles for select dropdown
     */
    public function roles(): JsonResponse
    {
        return $this->successResponse(
            $this->service->getRoles(),
            'select_options.roles.retrieved'
        );
    }

    /**
     * Get permissions for select dropdown
     */
    public function permissions(): JsonResponse
    {
        return $this->successResponse(
            $this->service->getPermissions(),
            'select_options.permissions.retrieved'
        );
    }

    /**
     * Get users for select dropdown
     */
    public function users(): JsonResponse
    {
        return $this->successResponse(
            $this->service->getUsers(),
            'select_options.users.retrieved'
        );
    }
}

