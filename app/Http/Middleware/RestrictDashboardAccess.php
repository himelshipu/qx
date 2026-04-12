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
     * Users can access the dashboard only if they have a role with permissions assigned.
     * Specifically, they must have the 'dashboard.view' permission through one of their roles.
     *
     * Brand and Influencer roles have NO permissions by default and cannot access the dashboard.
     * Admin and custom roles with permissions can access the dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            return redirect('/login');
        }

        // Get all roles assigned to the user with their permissions
        $userRoles = $user->roles()->with('permissions')->get();

        // If user has no roles assigned, deny access
        if ($userRoles->isEmpty()) {
            return redirect('/')->with('error', 'You do not have dashboard access. Please contact administrator.');
        }

        // Check if any of the user's roles have the 'dashboard.view' permission
        $hasDashboardPermission = false;
        foreach ($userRoles as $role) {
            if ($role->permissions()->where('slug', 'dashboard.view')->exists()) {
                $hasDashboardPermission = true;
                break;
            }
        }

        // If user has dashboard.view permission through any role, allow access
        if ($hasDashboardPermission) {
            return $next($request);
        }

        // Otherwise, deny access

        return redirect('/')->with('error', 'You do not have permission to access the dashboard.');
    }
}
