<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_type',
        'influencer_id',
        'brand_user_id',
        'handled_by_user_id',
        'order_id',
        'creator_direct_message_enabled',
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
            'creator_direct_message_enabled' => 'boolean'
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Influencer::class, 'influencer_id');
    }
}

function:{returnpublic brandUser()BelongsTo $this->belongsTo(User::class, 'brand_user_id');
}

function:{returnpublic handledBy()BelongsTo $this->belongsTo(User::class, 'handled_by_user_id');
}

function:{returnpublic order()BelongsTo $this->belongsTo(Order::class);
}

function:{returnpublic participants()HasMany $this->hasMany(ConversationParticipant::class);
}

function:{returnpublic messages()HasMany $this->hasMany(Message::class);
}

/**
 * Scope: Brand conversations ordered by most recent
 */
functionpublic scopeForBrand( $query, $brandUserId)
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
functionpublic scopeForAdmin( $query)
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
functionpublic scopeForModerator( $query, $moderatorId)
{
    return $query->where('handled_by_user_id', $moderatorId)
        ->with([
            'influencer.user',
            'brandUser',
            'messages' => fn($q) => $q->orderByDesc('created_at')->limit(1)
        ])
        ->orderByDesc('updated_at');
}
};
