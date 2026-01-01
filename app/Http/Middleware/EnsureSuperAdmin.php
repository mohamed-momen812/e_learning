<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Core\Exceptions\BusinessException;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            throw new BusinessException(
                'auth.unauthenticated',
                [],
                401
            );
        }

        if (!$user->isSuperAdmin()) {
            throw new BusinessException(
                'auth.unauthorized_super_admin_access',
                [],
                403
            );
        }

        return $next($request);
    }
}
