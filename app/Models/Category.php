<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        'sort_order',
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
