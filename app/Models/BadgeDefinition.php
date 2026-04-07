<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BadgeDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean'
        ];
    }

    public function influencers(): BelongsToMany
    {
        return $this->belongsToMany(Influencer::class, 'creator_badges')->withPivot(['earned_at', 'is_active'])->withTimestamps();
    }
}
