<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_path',
        'image_path',
        'is_active',
        'is_featured',
        'featured_order'
    ];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'is_featured' => 'boolean'
        ];
    }

    /**
     * Scope a query to active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to inactive categories.
     */
    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to featured categories.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to non-featured categories.
     */
    public function scopeNonFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', false);
    }

    /**
     * Scope a query for dashboard list payload.
     */
    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'name',
            'slug',
            'description',
            'icon_path',
            'image_path',
            'is_active',
            'is_featured',
            'featured_order',
            'updated_at',
        ])->withCount(['influencers', 'campaigns', 'onboardingProfiles']);
    }

    /**
     * Scope a query by search term.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('name', 'like', '%' . $term . '%')
                ->orWhere('slug', 'like', '%' . $term . '%')
                ->orWhere('description', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope a query by dashboard status filter.
     */
    public function scopeDashboardStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            'active' => $query->active(),
            'inactive' => $query->inactive(),
            default => $query,
        };
    }

    /**
     * Scope a query by featured filter.
     */
    public function scopeDashboardFeatured(Builder $query, string $featured): Builder
    {
        return match ($featured) {
            'featured' => $query->featured(),
            'non-featured' => $query->nonFeatured(),
            default => $query,
        };
    }

    /**
     * Scope a query ordered for dashboard listing.
     */
    public function scopeDashboardOrder(Builder $query): Builder
    {
        return $query->orderBy('name');
    }

    public function influencers(): BelongsToMany
    {
        return $this->belongsToMany(Influencer::class, 'influencer_categories', 'category_id', 'influencer_id')->withTimestamps();
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_categories')->withTimestamps();
    }

    public function onboardingProfiles(): BelongsToMany
    {
        return $this->belongsToMany(BrandOnboardingProfile::class, 'brand_onboarding_industries')->withTimestamps();
    }
}
