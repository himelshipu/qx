<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Influencer;
use App\Models\User;
use App\Services\Auth\PendingPostAuthActionService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    private const POST_AUTH_REDIRECT_KEY = 'auth_post_login_redirect';

    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $userType = $request->query('user-type', 'brand');

        // Validate user type - fallback to brand if invalid
        if (!in_array($userType, ['brand', 'influencer'])) {
            $userType = 'brand';
        }

        $redirectTo = $this->rememberPostAuthRedirect($request);

        return view('auth.register', ['userType' => $userType, 'redirectTo' => $redirectTo]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password'   => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type'  => ['required', 'in:brand,influencer'],
            'brand_name' => ['nullable', 'string', 'max:255'],
            'facebook'   => ['nullable', 'string', 'max:255'],
            'tiktok'     => ['nullable', 'string', 'max:255'],
            'linkedin'   => ['nullable', 'string', 'max:255'],
            'instagram'  => ['nullable', 'string', 'max:255']
        ]);

        $user = User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'user_type' => $request->user_type ?? 'brand'
        ]);

        // Auto-create Brand or Influencer record based on user_type
        if ($user->user_type === 'brand') {
            Brand::create([
                'user_id'    => $user->id,
                'brand_name' => $request->input('brand_name', $request->name)
            ]);
        } elseif ($user->user_type === 'influencer') {
            $influencer = Influencer::create([
                'user_id'      => $user->id,
                'display_name' => $request->name
            ]);

            // Create social links record if any social media was provided
            $influencer->socialLinks()->create([
                'facebook_url'  => $request->input('facebook'),
                'tiktok_url'    => $request->input('tiktok'),
                'linkedin_url'  => $request->input('linkedin'),
                'instagram_url' => $request->input('instagram')
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        $pendingActionRedirect = app(PendingPostAuthActionService::class)->consume($user);
        if ($pendingActionRedirect instanceof RedirectResponse) {
            return $pendingActionRedirect;
        }

        if (!$user->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        if ($user->user_type === 'brand') {
            $brand = $user->brand?->load('onboardingProfile');
            if (!$this->hasCompletedBrandSetup($brand)) {
                return redirect()->route('brand-setup.show');
            }
        }

        $postAuthRedirect = $this->consumePostAuthRedirect($request);
        if ($postAuthRedirect !== null) {
            return redirect($postAuthRedirect);
        }

        return redirect()->intended(route('home', absolute: false));
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
