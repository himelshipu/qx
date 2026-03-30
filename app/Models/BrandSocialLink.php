<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandSocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'linkedin_url',
        'x_url',
        'youtube_url',
        'other_url'
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
