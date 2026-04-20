<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * This middleware checks if the authenticated user has a specific permission.
     * Usage in routes: Route::middleware("permission:permission.slug")->...
     *
     * @param Request $request
     * @param Closure $next
     * @param string|null $permission The permission slug to check
     * @return Response
     */
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        $user = auth()->user();

        // If no user is authenticated, redirect to login
        if (!$user) {
            return redirect('login')->with('error', 'Please login first.');
        }

        // If no permission is specified, allow access
        if (!$permission) {
            return $next($request);
        }

        // Check if user has the permission
        if (!$user->hasPermission($permission)) {
            // Return 403 Forbidden if JSON request, otherwise redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'message' => 'Unauthorized: You do not have permission to perform this action.',
                    'permission_required' => $permission
                ], 403);
            }

            return redirect('/')
                ->with('error', 'You do not have permission to access this resource.')
                ->with('permission_required', $permission);
        }

        return $next($request);
    }
}
