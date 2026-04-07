<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_user_id',
        'sender_role',
        'on_behalf_of_influencer_id',
        'message',
        'attachment_path',
        'read_at'
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime'
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }

    public function onBehalfOfInfluencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class, 'on_behalf_of_influencer_id');
    }

    /**
     * Scope: Get paginated messages for a conversation with eager loading
     */
    public function scopeForConversation($query, $conversationId, $perPage = 20)
    {
        return $query->where('conversation_id', $conversationId)
            ->with('sender')
            ->orderBy('created_at')
            ->paginate($perPage);
    }

    /**
     * Scope: Get unread messages for a conversation
     */
    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }
}
