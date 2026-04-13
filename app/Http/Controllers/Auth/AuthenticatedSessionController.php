<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\PendingPostAuthActionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    private const POST_AUTH_REDIRECT_KEY = 'auth_post_login_redirect';

    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $redirectTo = $this->rememberPostAuthRedirect($request);

        return view('auth.login', ['redirectTo' => $redirectTo]);
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

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($userType === 'brand') {
            $brand = $user->brand?->load('onboardingProfile');
            if (!$this->hasCompletedBrandSetup($brand)) {
                return redirect()->route('brand-setup.show');
            }
        }

        // Redirect based on user type
        if (in_array($userType, ['admin', 'superadmin', 'moderator'])) {
            return redirect()->intended(route('dashboard.index', absolute: false));
        }

        // Brand and Influencer users go to frontend
        $postAuthRedirect = $this->consumePostAuthRedirect($request);
        if ($postAuthRedirect !== null) {
            return redirect($postAuthRedirect);
        }

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

    private function hasCompletedBrandSetup(?Brand $brand): bool
    {
        if (!$brand) {
            return false;
        }

        $profile = $brand->onboardingProfile;
        if (!$profile) {
            return false;
        }

        return (bool) $profile->is_completed
        || !empty($profile->objective)
        || !empty($profile->budget_range)
        || !empty($profile->business_type)
        || !empty($profile->company_size)
        || $profile->categories()->exists();
    }

    private function rememberPostAuthRedirect(Request $request): ?string
    {
        $candidate = $this->resolveRedirectCandidate($request);

        if ($candidate !== null && !$this->isAuthRoutePath($candidate)) {
            $request->session()->put(self::POST_AUTH_REDIRECT_KEY, $candidate);

            return $candidate;
        }

        return null;
    }

    private function consumePostAuthRedirect(Request $request): ?string
    {
        $candidate = (string) $request->session()->pull(self::POST_AUTH_REDIRECT_KEY, '');

        if ($candidate !== '' && str_starts_with($candidate, url('/')) && !$this->isAuthRoutePath($candidate)) {
            return $candidate;
        }

        return null;
    }

    private function resolveRedirectCandidate(Request $request): ?string
    {
        $explicit = (string) $request->input('redirect_to', $request->query('redirect', ''));
        if ($explicit !== '' && str_starts_with($explicit, url('/'))) {
            return $explicit;
        }

        $previous = (string) url()->previous();
        if ($previous !== '' && str_starts_with($previous, url('/'))) {
            return $previous;
        }

        return null;
    }

    private function isAuthRoutePath(string $url): bool
    {
        $path = (string) parse_url($url, PHP_URL_PATH);

        return $path === '/login'
            || $path === '/register'
            || $path === '/forgot-password'
            || str_starts_with($path, '/reset-password')
            || $path === '/email/verify'
            || $path === '/verify-email';
    }
}
