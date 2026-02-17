<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Creator;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $userType = $request->query('user-type', 'brand');
        
        // Validate user type - fallback to brand if invalid
        if (!in_array($userType, ['brand', 'creator'])) {
            $userType = 'brand';
        }
        
        return view('auth.register', ['userType' => $userType]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_type' => ['required', 'in:brand,creator'],
            'brand_name' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type ?? 'brand',
        ]);

        // Auto-create Brand or Creator record based on user_type
        if ($user->user_type === 'brand') {
            Brand::create([
                'user_id' => $user->id,
                'brand_name' => $request->input('brand_name', $request->name),
            ]);
        } elseif ($user->user_type === 'creator') {
            Creator::create([
                'user_id' => $user->id,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('verification.notice', absolute: false));
    }
}
