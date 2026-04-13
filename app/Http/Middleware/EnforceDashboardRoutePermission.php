<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceDashboardRoutePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect('/login');
        }

        if ($user->isSuperadmin()) {
            return $next($request);
        }

        $permission = $this->resolvePermissionFromRoute((string) $request->route()?->getName());

        // Skip check only when route is outside dashboard naming.
        if ($permission === null) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: You do not have permission to access this route.',
                    'permission_required' => $permission,
                ], 403);
            }

            abort(403, 'Unauthorized. Missing permission: ' . $permission);
        }

        return $next($request);
    }

    private function resolvePermissionFromRoute(string $routeName): ?string
    {
        if (!str_starts_with($routeName, 'dashboard.')) {
            return null;
        }

        $slug = substr($routeName, strlen('dashboard.'));

        $map = [
            'index' => 'dashboard.view',
            'campaigns.standard' => 'campaigns.index',
            'campaigns.standard.create' => 'campaigns.create',
            'settings.recovery.restore' => 'settings.restore',
            // Internal permission/menu helper APIs should require dashboard access.
            'api.menu' => 'dashboard.view',
            'api.permissions' => 'dashboard.view',
            'api.check-permission' => 'dashboard.view',
            'api.check-action' => 'dashboard.view',
        ];

        return $map[$slug] ?? $slug;
    }
}
