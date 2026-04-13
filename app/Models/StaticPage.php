<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope to get only active pages.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get pages by slug.
     */
    public function scopeBySlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    /**
     * Scope to filter by search term.
     */
    public function scopeSearch($query, $term)
    {
        return $query->where('title', 'like', "%{$term}%")
            ->orWhere('slug', 'like', "%{$term}%")
            ->orWhere('content', 'like', "%{$term}%");
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
            ? substr(strip_tags($this->content), 0, 150) . '...'
            : strip_tags($this->content);
    }
}
