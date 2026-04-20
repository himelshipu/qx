<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image_path',
        'meta_description',
        'meta_keywords',
        'is_published',
        'is_featured',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'published_at' => 'datetime',
            'sort_order' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeForDashboard($query)
    {
        return $query->select([
            'id',
            'author_id',
            'title',
            'slug',
            'excerpt',
            'featured_image_path',
            'is_published',
            'is_featured',
            'sort_order',
            'published_at',
            'created_at',
            'updated_at',
            'deleted_at',
        ]);
    }

    public function scopeDashboardStatus($query, string $status): Builder
    {
        return match ($status) {
            'published' => $query->where('is_published', true),
            'draft' => $query->where('is_published', false),
            'trashed' => $query->onlyTrashed(),
            default => $query,
        };
    }

    public function scopeDashboardOrder($query)
    {
        return $query->orderByDesc('is_published')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($builder) use ($term): void {
            $builder->where('title', 'like', "%{$term}%")
                ->orWhere('slug', 'like', "%{$term}%")
                ->orWhere('excerpt', 'like', "%{$term}%")
                ->orWhere('content', 'like', "%{$term}%");
        });
    }

    public function setSlugAttribute($value): void
    {
        $source = trim((string) $value) !== ''
            ? (string) $value
            : (string) ($this->attributes['title'] ?? '');

        $this->attributes['slug'] = Str::slug($source) ?: 'blog-post';
    }

    public function getSummaryAttribute(): string
    {
        $excerpt = $this->excerpt ?: strip_tags((string) $this->content);

        return Str::limit(trim((string) $excerpt), 160);
    }
}
