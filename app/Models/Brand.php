<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'brand_name',
        'bio',
        'industry',
        'website',
        'is_verified',
        'is_active',
        'profile_image_path',
        'cover_image_path',
        'setup_data'
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_active'   => 'boolean',
            'setup_data'  => 'array',
            'created_at'  => 'datetime',
            'updated_at'  => 'datetime'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function socialLinks(): HasOne
    {
        return $this->hasOne(BrandSocialLink::class);
    }

    public function billingProfiles(): MorphMany
    {
        return $this->morphMany(BillingProfile::class, 'user', 'user_type', 'user_id');
    }

    public function onboardingProfile(): HasOne
    {
        return $this->hasOne(BrandOnboardingProfile::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }
}
