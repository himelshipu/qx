<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InfluencerPlatformStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'influencer_id',
        'platform',
        'handle',
        'profile_url',
        'follower_count',
        'avg_views',
        'engagement_rate',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'follower_count'  => 'integer',
            'avg_views'       => 'integer',
            'engagement_rate' => 'decimal:2',
            'is_active'       => 'boolean'
        ];
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class, 'influencer_id');
    }
}
