<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_path',
        'image_path',
        'is_active',
        'sort_order'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean'
        ];
    }

    public function creators(): BelongsToMany
    {
        return $this->belongsToMany(Creator::class, 'creator_categories')->withTimestamps();
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
