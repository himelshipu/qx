<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'type',
        'title',
        'body',
        'data_json',
        'notifiable_type',
        'notifiable_id',
        'is_read',
        'read_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'data_json'  => 'array',
        'is_read'    => 'boolean',
        'read_at'    => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relationship: Notification belongs to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope: Get only unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope: Get only read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope: Get notifications by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        }
    }

    /**
     * Mark notification as unread
     */
    public function markAsUnread(): void
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    /**
     * Get the action URL from data JSON
     */
    public function getActionUrl(): string
    {
        return $this->data_json['action_url'] ?? '/dashboard/notifications';
    }

    /**
     * Get icon class from data JSON or default based on type
     */
    public function getIconClass(): string
    {
        return $this->data_json['icon_class'] ?? $this->getIcon();
    }

    /**
     * Get color class from data JSON or default based on type
     */
    public function getColorClass(): string
    {
        return $this->data_json['color_class'] ?? $this->getColor();
    }

    /**
     * Get icon based on notification type
     */
    public function getIcon(): string
    {
        return match ($this->type) {
            'order'    => 'shopping-cart',
            'payment'  => 'dollar-sign',
            'campaign' => 'megaphone',
            'message'  => 'message-circle',
            'review'   => 'star',
            'payout'   => 'wallet',
            default    => 'bell',
        };
    }

    /**
     * Get color class based on type
     */
    public function getColor(): string
    {
        return match ($this->type) {
            'order'    => 'emerald',
            'payment'  => 'green',
            'campaign' => 'blue',
            'message'  => 'indigo',
            'review'   => 'yellow',
            'payout'   => 'purple',
            default    => 'blue',
        };
    }
}
