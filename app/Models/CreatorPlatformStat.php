<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorPlatformStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id',
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

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }
}
