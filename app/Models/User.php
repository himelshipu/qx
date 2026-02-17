<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'verification_code',           
        'verification_code_expires_at', 
        'email_verified_at',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'verification_code_expires_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the Brand associated with the user.
     */
    public function brand()
    {
        return $this->hasOne(\App\Models\Brand::class);
    }

    /**
     * Get the Creator associated with the user.
     */
    public function creator()
    {
        return $this->hasOne(\App\Models\Creator::class);
    }

    /**
     * Check if the user has verified their email.
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark the user's email as verified.
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'verification_code' => null,
            'verification_code_expires_at' => null,
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the verification code email notification.
     */
    public function sendVerificationCodeNotification(): void
    {
        // Prevent duplicate verification emails sent within 10 seconds
        if ($this->verification_code && $this->verification_code_expires_at) {
            $timeSinceLastCode = now()->diffInSeconds($this->verification_code_expires_at->subMinutes(2));
            if ($timeSinceLastCode < 10) {
                // Email was already sent recently, skip to prevent duplicates
                return;
            }
        }

        // Generate a 6-digit verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code with 2 minutes expiration
        $this->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => now()->addMinutes(2),
        ]);

        // Send the verification code via email
        \Illuminate\Support\Facades\Mail::send(
            new \App\Mail\SendVerificationCodeMail($this, $verificationCode)
        );
    }

    /**
     * Override Laravel's default email verification notification to use our custom verification code method.
     * This ensures that only one verification email is sent with the same code.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->sendVerificationCodeNotification();
    }
}
