<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendVerificationCodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class VerificationCodeController extends Controller
{
    /**
     * Show the verification code forms.
     */
    public function show(Request $request)
    {
        return view('auth.verify-code');
    }

    /**
     * Send a new verification code to the user's email.
     */
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user is already verified
        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('success', 'Your email is already verified.');
        }

        // Generate a 6-digit verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code with 15 minutes expiration
        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        // Send the verification code via email (synchronous)
        try {
            Mail::send(new SendVerificationCodeMail($user, $verificationCode));
        } catch (\Exception $e) {
            \Log::error('Failed to send verification code email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again.']);
        }

        return back()
            ->with('email', $user->email)
            ->with('success', 'Verification code sent to your email. Valid for 15 minutes.');
    }

    /**
     * Verify the code entered by the user.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'code' => 'required|string|size:6',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user is already verified
        if ($user->hasVerifiedEmail()) {
            return redirect(route('dashboard'))->with('success', 'Your email is already verified.');
        }

        // Check if verification code is valid
        if (!$user->verification_code || $user->verification_code !== $request->code) {
            throw ValidationException::withMessages([
                'code' => 'The verification code is incorrect.',
            ]);
        }

        // Check if verification code has expired
        if ($user->verification_code_expires_at && $user->verification_code_expires_at < now()) {
            throw ValidationException::withMessages([
                'code' => 'The verification code has expired. Please request a new one.',
            ]);
        }

        // Mark email as verified
        $user->update([
            'email_verified_at' => now(),
            'verification_code' => null,
            'verification_code_expires_at' => null,
        ]);

        // Refresh the authenticated user in the session
        Auth::setUser($user->refresh());

        // Determine redirect based on user type
        if ($user->user_type === 'brand') {
            // Brand users go to setup
            return redirect(route('brand-setup.show'))->with('success', 'Your email has been verified successfully!');
        } else {
            // Creator users go directly to dashboard
            return redirect(route('dashboard'))->with('success', 'Your email has been verified successfully!');
        }
    }
}
