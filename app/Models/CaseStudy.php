<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseStudy extends Model
{
    use SoftDeletes;

    protected $table = 'case_studies';

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'cover_image_path',
        'external_url',
        'is_published',
        'sort_order',
        'published_at'
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'sort_order'   => 'integer',
            'published_at' => 'datetime'
        ];
    }

    /**
     * Scope a query for dashboard listing payload.
     */
    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'title',
            'slug',
            'summary',
            'cover_image_path',
            'external_url',
            'is_published',
            'sort_order',
            'published_at',
            'updated_at',
        ]);
    }

    /**
     * Scope a query by search term.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('title', 'like', '%' . $term . '%')
                ->orWhere('summary', 'like', '%' . $term . '%')
                ->orWhere('slug', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope dashboard status filter.
     */
    public function scopeDashboardStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            'published' => $query->where('is_published', true),
            'draft' => $query->where('is_published', false),
            default => $query,
        };
    }

    /**
     * Scope dashboard ordering.
     */
    public function scopeDashboardOrder(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('published_at');
    }

    /**
     * Get published case studies ordered by sort order
     */
    public static function getPublished()
    {
        return self::query()
            ->where('is_published', true)
            ->dashboardOrder()
            ->get();
    }
}
