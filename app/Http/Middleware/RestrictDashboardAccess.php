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
     * RBAC Dashboard Access Rules:
     * 1. User must be authenticated
     * 2. User must have verified email
     * 3. User must be ACTIVE (is_active = true)
     * 4. User MUST NOT be 'brand' or 'influencer' type (completely blocked)
     * 5. User must have 'admin', 'moderator', or 'superadmin' user_type
     * 6. User must have at least one role assigned
     * 7. User must have 'dashboard.view' permission through roles OR be superadmin
     *
     * Superadmin bypasses permission check due to isSuperadmin() method.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // CHECK 1: User must be authenticated
        if (!$user) {
            return redirect('/login')->with('error', 'Please login to access the dashboard.');
        }

        // CHECK 2: User must be verified (email verified)
        if (!$user->hasVerifiedEmail()) {
            return redirect('/verify-email')->with('error', 'Please verify your email to access the dashboard.');
        }

        // CHECK 3: User must be active
        if (!$user->is_active) {
            return redirect('/')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        // CHECK 4: Brand and Influencer users are COMPLETELY BLOCKED
        // These user types should never access the admin dashboard
        if ($user->user_type === 'brand' || $user->user_type === 'influencer') {
            return redirect('/')
                ->with('error', 'You do not have permission to access the dashboard.');
        }

        // CHECK 5: Only admin, moderator, and superadmin user_types allowed
        if (!in_array($user->user_type, ['admin', 'moderator', 'superadmin'])) {
            return redirect('/')
                ->with('error', 'Only administrators and moderators can access the dashboard.');
        }

        // CHECK 6: User must have at least one role assigned
        if ($user->roles()->count() === 0) {
            return redirect('/')
                ->with('error', 'You do not have a role assigned. Please contact an administrator.');
        }

        // CHECK 7: User must have dashboard.view permission or be superadmin
        // Superadmin has all permissions by default (hasPermission checks isSuperadmin first)
        if (!$user->hasPermission('dashboard.view')) {
            return redirect('/')
                ->with('error', 'Your role does not have permission to access the dashboard.');
        }

        return $next($request);
    }
}
