<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RestrictDashboardAccess
{
    /**
     * Handle an incoming request.
     *
     * Only admin, superadmin, and moderator users can access the dashboard.
     * Brand and creator users are redirected to the frontend.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Allow admin, superadmin, and moderator
        if (in_array($user->user_type, ['admin', 'superadmin', 'moderator'])) {
            return $next($request);
        }

        // Redirect brand and creator to frontend

        return redirect('/');
    }
}
