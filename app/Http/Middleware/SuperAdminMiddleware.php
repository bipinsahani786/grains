<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SuperAdminMiddleware
{
    /**
     * Ensure the authenticated user is a super_admin.
     * In this codebase, super admin is identified by company_id = null.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        $user = auth()->user();

        // Super admin is identified by company_id being null
        if (!is_null($user->company_id)) {
            abort(403, 'Unauthorized. Super Admin access required.');
        }

        return $next($request);
    }
}
