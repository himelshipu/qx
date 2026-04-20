<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaqItem extends Model
{
    protected $fillable = [
        'faq_section_id',
        'question',
        'answer',
        'sort_order',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active'  => 'boolean'
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(FaqSection::class, 'faq_section_id');
    }

    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'faq_section_id',
            'question',
            'answer',
            'sort_order',
            'is_active',
            'created_at',
        ]);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if ($search === '') {
            return $query;
        }

        return $query->where('question', 'like', "%{$search}%");
    }

    public function scopeDashboardStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            default => $query,
        };
    }

    public function scopeDashboardOrder(Builder $query): Builder
    {
        return $query
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');
    }
}
