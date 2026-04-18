<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'brand_name',
        'bio',
        'industry',
        'website',
        'is_verified',
        'is_featured',
        'featured_order',
        'is_active',
        'profile_image_path',
        'cover_image_path',
        'setup_data',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_verified' => 'boolean',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'setup_data' => 'array',
            'featured_order' => 'integer',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
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

    public function scopeSearchDashboard(Builder $query, string $search): Builder
    {
        $term = trim($search);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use ($term): void {
            $subQuery
                ->where('brand_name', 'like', "%{$term}%")
                ->orWhere('industry', 'like', "%{$term}%")
                ->orWhereHas('user', function (Builder $userQuery) use ($term): void {
                    $userQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%")
                        ->orWhere('country', 'like', "%{$term}%");
                });
        });
    }

    public function scopeFilterStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            'active' => $query->whereHas('user', fn (Builder $userQuery): Builder => $userQuery->where('is_active', true)),
            'inactive' => $query->whereHas('user', fn (Builder $userQuery): Builder => $userQuery->where('is_active', false)),
            default => $query,
        };
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    public function scopeNonFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', false);
    }

    public function scopeDashboardOrder(Builder $query): Builder
    {
        return $query
            ->orderByRaw('COALESCE(sort_order, 0) ASC')
            ->orderByDesc('updated_at');
    }
}
