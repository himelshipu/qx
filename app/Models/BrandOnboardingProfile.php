<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BrandOnboardingProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'objective',
        'budget_range',
        'business_type',
        'company_size',
        'is_completed',
        'completed_at'
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime'
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'brand_onboarding_industries')->withTimestamps();
    }
}
