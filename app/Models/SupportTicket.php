<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
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

    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->with(['requester', 'assignedTo', 'category']);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($search): void {
            $builder->where('subject', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('ticket_number', 'like', "%{$search}%");
        });
    }

    public function scopeDashboardStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeDashboardCategory(Builder $query, ?int $categoryId): Builder
    {
        return $categoryId ? $query->where('support_category_id', $categoryId) : $query;
    }

    public function scopeDashboardPriority(Builder $query, ?string $priority): Builder
    {
        return $priority ? $query->where('priority', $priority) : $query;
    }

    public function scopeDashboardOrder(Builder $query): Builder
    {
        return $query->orderByDesc('created_at');
    }

    /**
     * Scope to get open tickets
     */
    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope to get resolved tickets
     */
    public function scopeResolved(Builder $query): Builder
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope to get tickets by category
     */
    public function scopeByCategory(Builder $query, mixed $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeNewForSidebar(Builder $query): Builder
    {
        return $query->dashboardStatus(config('support-ticket.new_status', 'open'))->whereNull('assigned_to_user_id');
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'open' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            'in_progress' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
            'waiting_user' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
            'resolved' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            'closed' => 'bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match ($this->priority) {
            'low' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
            'medium' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
            'high' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'urgent' => 'bg-red-200 text-red-800 dark:bg-red-900 dark:text-red-200',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    public function statusLabel(): string
    {
        return ucfirst(str_replace('_', ' ', $this->status));
    }

    public function priorityLabel(): string
    {
        return ucfirst($this->priority);
    }
}
