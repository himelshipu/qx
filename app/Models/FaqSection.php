<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqSection extends Model
{
    protected $fillable = [
        'section_code',
        'section_title',
        'audience_type',
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

    public function items(): HasMany
    {
        return $this->hasMany(FaqItem::class, 'faq_section_id');
    }

    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'section_code',
            'section_title',
            'audience_type',
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

        return $query->where(function (Builder $builder) use ($search): void {
            $builder->where('section_title', 'like', "%{$search}%")
                ->orWhere('section_code', 'like', "%{$search}%");
        });
    }

    public function scopeDashboardStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            default => $query,
        };
    }

    public function scopeDashboardAudience(Builder $query, string $audience): Builder
    {
        return match ($audience) {
            'all', 'brand', 'influencer' => $query->where('audience_type', $audience),
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
