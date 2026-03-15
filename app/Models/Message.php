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
        'on_behalf_of_creator_id',
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

    public function onBehalfOfCreator(): BelongsTo
    {
        return $this->belongsTo(Creator::class, 'on_behalf_of_creator_id');
    }
}
