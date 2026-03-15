<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FollowerRange extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'label',
        'min_followers',
        'max_followers',
        'sort_order',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'min_followers' => 'integer',
            'max_followers' => 'integer',
            'sort_order'    => 'integer',
            'is_active'     => 'boolean'
        ];
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_target_follower_ranges')->withTimestamps();
    }
}
