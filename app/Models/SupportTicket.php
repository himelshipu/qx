<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'ticket_number',
        'requester_user_id',
        'support_category_id',
        'assigned_to_user_id',
        'subject',
        'description',
        'priority',
        'status',
        'source',
        'resolved_at',
        'closed_at'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at'   => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime'
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->ticket_number) {
                $model->ticket_number = 'TK-' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the user who submitted the ticket
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    /**
     * Get the admin/moderator assigned to this ticket
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /**
     * Get the support category
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(SupportCategory::class, 'support_category_id');
    }

    /**
     * Scope to get open tickets
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope to get resolved tickets
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope to get tickets by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
}
