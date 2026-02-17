<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
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
     * Send the email verification notification.
     * Override Laravel's default to use our verification code system.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->sendVerificationCodeNotification();
    }

    /**
     * Send the verification code email notification.
     */
    public function sendVerificationCodeNotification(): void
    {
        // Generate a 6-digit verification code
        $verificationCode = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store the code with 15 minutes expiration
        $this->update([
            'verification_code' => $verificationCode,
            'verification_code_expires_at' => now()->addMinutes(15),
        ]);

        // Send the verification code via email
        \Illuminate\Support\Facades\Mail::send(
            new \App\Mail\SendVerificationCodeMail($this, $verificationCode)
        );
    }
}
