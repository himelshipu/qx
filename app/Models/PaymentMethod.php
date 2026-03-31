<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'provider',
        'provider_payment_method_id',
        'last4',
        'brand',
        'expiry_month',
        'expiry_year',
        'is_default'
    ];

    protected function casts(): array
    {
        return [
            'expiry_month' => 'integer',
            'expiry_year' => 'integer',
            'is_default' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    /**
     * User who owns this payment method
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable card display
     * Example: "Visa ending in 4242"
     */
    public function getDisplayNameAttribute(): string
    {
        return ucfirst($this->brand) . " ending in " . $this->last4;
    }

    /**
     * Get expiry date as formatted string
     * Example: "03/29"
     */
    public function getFormattedExpiryAttribute(): string
    {
        if (!$this->expiry_month || !$this->expiry_year) {
            return 'N/A';
        }

        return sprintf('%02d/%02d', $this->expiry_month, substr($this->expiry_year, -2));
    }

    /**
     * Check if card is expired
     */
    public function isExpired(): bool
    {
        if (!$this->expiry_month || !$this->expiry_year) {
            return false;
        }

        $now = now();
        // Card expires at end of expiry month
        $expiryDate = \Carbon\Carbon::createFromDate($this->expiry_year, $this->expiry_month, 1)
            ->endOfMonth()
            ->endOfDay();

        return $now->isAfter($expiryDate);
    }

    /**
     * Check if card is expiring soon (within 30 days)
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_month || !$this->expiry_year) {
            return false;
        }

        $now = now();
        // Card expires at end of expiry month
        $expiryDate = \Carbon\Carbon::createFromDate($this->expiry_year, $this->expiry_month, 1)
            ->endOfMonth()
            ->endOfDay();
        
        $thirtyDaysFromNow = $now->copy()->addDays(30);

        return $now->isBefore($expiryDate) && $thirtyDaysFromNow->isAfter($expiryDate);
    }

    /**
     * Scope: only stripe cards
     */
    public function scopeStripe($query)
    {
        return $query->where('provider', 'stripe');
    }

    /**
     * Scope: only default cards
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Scope: only valid (not expired) cards
     */
    public function scopeValid($query)
    {
        return $query->whereRaw('YEAR(DATE_ADD(DATE_ADD(LAST_DAY(CONCAT_WS(\"-\", expiry_year, expiry_month, \"01\")), INTERVAL 1 DAY), INTERVAL -1 SECOND)) >= YEAR(NOW())')
            ->where(function ($q) {
                $q->whereRaw('YEAR(NOW()) < expiry_year')
                    ->orWhere(function ($q2) {
                        $q2->whereRaw('YEAR(NOW()) = expiry_year')
                            ->whereRaw('MONTH(NOW()) <= expiry_month');
                    });
            });
    }
}
