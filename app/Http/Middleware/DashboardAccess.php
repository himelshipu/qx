<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardAccess
{
    /**
     * Handle an incoming request.
     * Only admin and moderator users can access dashboard.
     * Brand and influencer users cannot access dashboard.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Check if user is authenticated and can access dashboard
        if (!$user || !$user->canAccessDashboard()) {
            return redirect()->route('frontend.home')
                ->with('error', 'You do not have permission to access the dashboard.');
        }

        return $next($request);
    }
}
