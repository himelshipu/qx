<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseArticle extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'badge',
        'summary',
        'content',
        'read_time_minutes',
        'is_featured',
        'is_published',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'read_time_minutes' => 'integer',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function (Builder $builder): void {
                $builder->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'title',
            'slug',
            'badge',
            'read_time_minutes',
            'is_featured',
            'is_published',
            'published_at',
            'sort_order',
            'created_at',
        ]);
    }

    public function scopeSearch(Builder $query, string $search): Builder
    {
        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($search): void {
            $builder->where('title', 'like', "%{$search}%")
                ->orWhere('badge', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        });
    }

    public function scopeDashboardStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            'published' => $query->where('is_published', true),
            'draft' => $query->where('is_published', false),
            default => $query,
        };
    }

    public function scopeDashboardFeatured(Builder $query, string $featured): Builder
    {
        return match ($featured) {
            'featured' => $query->where('is_featured', true),
            'regular' => $query->where('is_featured', false),
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
