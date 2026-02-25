<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SendVerificationCodeMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class VerificationCodeController extends Controller
{
    /**
     * Show the email verification page.
     */
    public function show(Request $request)
    {
        // Redirect if already verified
        if (Auth::user()->hasVerifiedEmail()) {
            if (Auth::user()->user_type === 'brand') {
                $brand = Auth::user()->brand;
                if ($brand) {
                    return redirect(route('dashboard.brands.view', $brand->id));
                }
                return redirect(route('dashboard.index'));
            }
            return redirect(route('dashboard.index'));
        }

        return view('auth.verify-email');
    }

    /**
     * Send a new verification code to the authenticated user.
     */
    public function send(Request $request)
    {
        $user = Auth::user();

        // Check if user is already verified
        if ($user->hasVerifiedEmail()) {
            return redirect('/')->with('success', 'Your email is already verified.');
        }

        // Generate a 6-digit verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code with 2 minutes expiration
        $user->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => now()->addMinutes(2),
        ]);

        // Send the verification code via email (synchronous)
        try {
            Mail::send(new SendVerificationCodeMail($user, $verificationCode));
        } catch (\Exception $e) {
            Log::error('Failed to send verification code email: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Failed to send verification code. Please try again.']);
        }

        return back()
            ->with('email', $user->email)
            ->with('success', 'Verification code sent to your email. Valid for 2 minutes.');
    }

    /**
     * Verify the code entered by the authenticated user.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $user = Auth::user();

        // Check if user is already verified
        if ($user->hasVerifiedEmail()) {
            if ($user->user_type === 'brand') {
                $brand = $user->brand;
                if ($brand) {
                    return redirect(route('dashboard.brands.view', $brand->id))->with('success', 'Your email is already verified.');
                }
                return redirect(route('dashboard.index'))->with('success', 'Your email is already verified.');
            }
            return redirect(route('dashboard.index'))->with('success', 'Your email is already verified.');
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

        // Mark email as verified and clear OTP
        $user->markEmailAsVerified();

        // Refresh the authenticated user in the session
        Auth::login($user, true);
        $brand = $user->brand;
            if ($brand) {
                return redirect(route('dashboard.brands.view', $brand->id))->with('success', 'Your email has been verified successfully!');
            }
            return redirect(route('dashboard.index'));
        // Determine redirect based on user type
        if ($user->user_type === 'brand') {
            return redirect(route('dashboard.brands.view'))->with('success', 'Your email has been verified successfully!');
        } else {
            return redirect(route('dashboard.index'))->with('success', 'Your email has been verified successfully!');
        }
    }
}
