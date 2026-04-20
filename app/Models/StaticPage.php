<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class StaticPage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'title',
        'slug',
        'content',
        'meta_description',
        'meta_keywords',
        'is_active',
        'sort_order',
        'show_on_footer',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'show_on_footer' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get only active pages.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get pages by slug.
     */
    public function scopeBySlug(Builder $query, string $slug): Builder
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope to filter by search term.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);
        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($term): void {
            $builder->where('title', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%");
        });
    }

    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'title',
            'slug',
            'content',
            'meta_description',
            'meta_keywords',
            'is_active',
            'updated_at',
            'created_at',
        ]);
    }

    public function scopeDashboardStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            'published' => $query->where('is_active', true),
            'draft' => $query->where('is_active', false),
            default => $query,
        };
    }

    public function scopeDashboardOrder(Builder $query): Builder
    {
        return $query->orderByDesc('updated_at')->orderByDesc('id');
    }

    /**
     * Get the slug from title if not explicitly set.
     */
    public function setSlugAttribute($value)
    {
        $source = trim((string) $value) !== '' ? (string) $value : (string) ($this->attributes['title'] ?? '');
        $this->attributes['slug'] = Str::slug($source);
    }

    /**
     * Accessor for display status.
     */
    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active
            ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>'
            : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Inactive</span>';
    }

    /**
     * Get a summary of the content (first 150 characters).
     */
    public function getContentSummaryAttribute(): string
    {
        return strlen($this->content) > 150
            ? substr(strip_tags($this->content), 0, 150).'...'
            : strip_tags($this->content);
    }
}
