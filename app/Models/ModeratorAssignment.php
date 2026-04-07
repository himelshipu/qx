<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ModeratorAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'moderator_user_id',
        'influencer_id',
        'assigned_at',
        'unassigned_at',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'unassigned_at' => 'datetime',
        ];
    }

    /**
     * Get the moderator user
     */
    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_user_id');
    }

    /**
     * Get the influencer being moderated
     */
    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }

    /**
     * Check if this assignment is currently active (not unassigned)
     */
    public function isActive(): bool
    {
        return is_null($this->unassigned_at);
    }
}
