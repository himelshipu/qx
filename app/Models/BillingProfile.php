<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BillingProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'legal_company_name',
        'vat_id',
        'billing_address',
        'billing_city',
        'billing_country',
        'billing_postal_code'
    ];

    public function user(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'user_type', 'user_id');
    }

    /**
     * Determine if this is a brand billing profile
     */
    public function isBrand(): bool
    {
        return $this->user_type === 'brand';
    }

    /**
     * Determine if this is a creator billing profile
     */
    public function isCreator(): bool
    {
        return $this->user_type === 'creator';
    }

    /**
     * Get formatted billing address
     */
    public function getFormattedAddressAttribute(): string
    {
        $parts = array_filter([
            $this->billing_address,
            $this->billing_city,
            $this->billing_country,
            $this->billing_postal_code
        ]);

        return implode(', ', $parts);
    }

    /**
     * Check if billing profile is complete
     */
    public function isComplete(): bool
    {
        return !empty($this->legal_company_name) &&
               !empty($this->billing_address) &&
               !empty($this->billing_city) &&
               !empty($this->billing_country) &&
               !empty($this->billing_postal_code);
    }
}
