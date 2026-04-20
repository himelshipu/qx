<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_type',
        'influencer_id',
        'brand_user_id',
        'handled_by_user_id',
        'order_id',
        'influencer_direct_message_enabled',
        'title',
        'public_id'
    ];

    protected static function booted()
    {
        static::creating(function ($conversation) {
            if (empty($conversation->public_id)) {
                $conversation->public_id = bin2hex(random_bytes(8));
            }
        });
    }

    protected function casts(): array
    {
        return [
            'influencer_direct_message_enabled' => 'boolean'
        ];
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class, 'influencer_id');
    }

    public function moderatorAssignment(): BelongsTo
    {
        return $this->belongsTo(ModeratorAssignment::class, 'handled_by_user_id', 'moderator_user_id');
    }

    public function brandUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'brand_user_id');
    }

    public function handledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by_user_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(ConversationParticipant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Scope: Brand conversations ordered by most recent
     */
    public function scopeForBrand(Builder $query, int $brandUserId): Builder
    {
        return $query->where('brand_user_id', $brandUserId)
            ->with([
                'influencer.user',
                'handledBy',
                'messages' => fn($q) => $q->orderByDesc('created_at')->limit(1)
            ])
            ->orderByDesc('updated_at');
    }

    /**
     * Scope: All conversations (admin view)
     */
    public function scopeForAdmin(Builder $query): Builder
    {
        return $query->with([
            'influencer.user',
            'brandUser',
            'handledBy',
            'messages' => fn($q) => $q->orderByDesc('created_at')->limit(1)
        ])
            ->orderByDesc('updated_at');
    }

    /**
     * Scope: Moderator's assigned conversations
     */
    public function scopeForModerator(Builder $query, int $moderatorId): Builder
    {
        return $query->where('handled_by_user_id', $moderatorId)
            ->with([
                'influencer.user',
                'brandUser',
                'messages' => fn($q) => $q->orderByDesc('created_at')->limit(1)
            ])
            ->orderByDesc('updated_at');
    }

    public function scopeWithUnreadMessagesForUser(Builder $query, User $user): Builder
    {
        return $query->whereHas('messages', function (Builder $messageQuery) use ($user): void {
            $messageQuery->unread()->where('sender_user_id', '!=', $user->id);
        });
    }

    public function scopeForSidebarUnreadCount(Builder $query, User $user): Builder
    {
        return match ($user->user_type) {
            'brand' => $query->where('brand_user_id', $user->id),
            'moderator' => $query->where('handled_by_user_id', $user->id),
            default => $query,
        };
    }
}
