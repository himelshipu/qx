<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\PendingPostAuthActionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * Redirects based on user type:
     * - admin/superadmin/moderator → /dashboard
     * - brand/influencer → /
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $pendingActionRedirect = app(PendingPostAuthActionService::class)->consume($request->user());

        if ($pendingActionRedirect instanceof RedirectResponse) {
            return $pendingActionRedirect;
        }

        $user     = $request->user();
        $userType = $user->user_type;

        // Redirect based on user type
        if (in_array($userType, ['admin', 'superadmin', 'moderator'])) {
            return redirect()->intended(route('dashboard.index', absolute: false));
        }

        // Brand and Influencer users go to frontend

        return redirect()->intended(route('home', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
