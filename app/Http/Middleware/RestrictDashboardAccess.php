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
     * Restrictions:
     * 1. Brand and Influencer users are completely blocked from dashboard
     * 2. Only admin and moderator users can access dashboard
     * 3. Users must be authenticated and verified
     * 4. Must have a role with 'dashboard.view' permission assigned
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // FIRST CHECK: User must be authenticated
        if (!$user) {
            return redirect('/login')->with('error', 'Please login to access the dashboard.');
        }

        // SECOND CHECK: User must be verified (email verified)
        if (!$user->hasVerifiedEmail()) {
            return redirect('/verify-email')->with('error', 'Please verify your email to access the dashboard.');
        }

        // THIRD CHECK: Brand and Influencer users are COMPLETELY BLOCKED
        // They should not have any access to admin dashboard
        if ($user->user_type === 'brand' || $user->user_type === 'influencer') {
            return redirect('/')
                ->with('error', 'You do not have permission to access the dashboard.');
        }

        // FOURTH CHECK: Only admin and moderator user types allowed
        if (!in_array($user->user_type, ['admin', 'moderator', 'superadmin'])) {
            return redirect('/')
                ->with('error', 'Only administrators and moderators can access the dashboard.');
        }

        // FIFTH CHECK: User must be active
        if (!$user->is_active) {
            return redirect('/')
                ->with('error', 'Your account has been deactivated. Please contact support.');
        }

        // SIXTH CHECK: User must have at least one role assigned
        if ($user->roles()->count() === 0) {
            return redirect('/')
                ->with('error', 'You do not have a role assigned. Please contact an administrator.');
        }

        // SEVENTH CHECK: User must have at least one role with dashboard.view permission
        $hasDashboardAccess = $user->roles()
            ->whereHas('permissions', fn($q) => $q->where('slug', 'dashboard.view'))
            ->exists();

        if (!$hasDashboardAccess) {
            return redirect('/')
                ->with('error', 'Your role does not have permission to access the dashboard.');
        }

        return $next($request);
    }
}
