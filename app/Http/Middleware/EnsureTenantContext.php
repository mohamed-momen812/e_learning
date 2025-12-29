<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Validate that tenant context is initialized
        if (!tenancy()->initialized) {
            return response()->json([
                'success' => false,
                'message' => __('tenant.context_not_initialized'),
            ], 400);
        }

        // Validate that the tenant exists
        $tenant = tenancy()->tenant;
        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => __('tenant.not_found'),
            ], 404);
        }

        return $next($request);
    }
}

