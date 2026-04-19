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
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'follower_count'  => 'integer',
            'is_active'       => 'boolean'
        ];
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class, 'influencer_id');
    }
}
