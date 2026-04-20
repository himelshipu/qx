<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_name',
        'author_role',
        'company_name',
        'quote',
        'rating',
        'is_published',
        'sort_order'
    ];

    protected function casts(): array
    {
        return [
            'rating'       => 'integer',
            'is_published' => 'boolean',
            'sort_order'   => 'integer'
        ];
    }

    /**
     * Scope listing columns for dashboard.
     */
    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'author_name',
            'author_role',
            'company_name',
            'quote',
            'rating',
            'is_published',
            'sort_order',
            'updated_at',
        ]);
    }

    /**
     * Scope dashboard search by author/company/quote.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder
                ->where('author_name', 'like', '%' . $term . '%')
                ->orWhere('company_name', 'like', '%' . $term . '%')
                ->orWhere('quote', 'like', '%' . $term . '%');
        });
    }

    /**
     * Scope dashboard publish filter.
     */
    public function scopeDashboardStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            'published' => $query->where('is_published', true),
            'unpublished' => $query->where('is_published', false),
            default => $query,
        };
    }

    /**
     * Scope default dashboard ordering.
     */
    public function scopeDashboardOrder(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('updated_at');
    }
}
