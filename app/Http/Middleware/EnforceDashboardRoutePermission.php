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
            'campaigns.standard.table' => 'campaigns.index',
            'categories.table' => 'categories.index',
            'categories-featured.list' => 'categories.index',
            'categories-featured.search' => 'categories.index',
            'categories-featured.add' => 'categories.update',
            'categories-featured.remove' => 'categories.update',
            'categories-featured.reorder' => 'categories.reorder',
            'testimonials.table' => 'testimonials.index',
            'featured-collaborations.table' => 'featured-collaborations.index',
            'featured-collaborations.reorder' => 'featured-collaborations.update',
            'knowledge-base.table' => 'knowledge-base.index',
            'knowledge-base.reorder' => 'knowledge-base.update',
            'faqs.sections.table' => 'faqs.sections.index',
            'faqs.items.table' => 'faqs.items.index',
            'users.table' => 'users.index',
            'support-tickets.table' => 'support-tickets.index',
            'packages.table' => 'packages.index',
            'packages.purchase.store' => 'packages.purchase',
            'blogs.table' => 'blogs.index',
            'blogs.toggle-featured' => 'blogs.toggle-status',
            'brands-featured.list' => 'brands.index',
            'brands-featured.search' => 'brands.index',
            'brands-featured.add' => 'brands.update',
            'brands-featured.remove' => 'brands.update',
            'brands-featured.reorder' => 'brands.reorder',
            'influencers-featured.list' => 'influencers.index',
            'influencers-featured.search' => 'influencers.index',
            'influencers-featured.add' => 'influencers.toggle-featured',
            'influencers-featured.remove' => 'influencers.toggle-featured',
            'influencers-featured.reorder' => 'influencers.toggle-featured',
            'payment-queue.mark-paid' => 'payment-queue.bulk-mark',
            'payouts.index' => 'payments.index',
            'settings.recovery.restore' => 'settings.restore',
            'settings.update-branding' => 'settings.update',
            'settings.update-email' => 'settings.update',
            'settings.update-platform' => 'settings.update',
            'settings.update-footer' => 'settings.update',
            'static-pages.table' => 'static-pages.index',
            // Internal permission/menu helper APIs should require dashboard access.
            'api.menu' => 'dashboard.view',
            'api.permissions' => 'dashboard.view',
            'api.check-permission' => 'dashboard.view',
            'api.check-action' => 'dashboard.view',
            'api.sidebar-badges' => 'dashboard.view',
        ];

        return $map[$slug] ?? $slug;
    }
}
