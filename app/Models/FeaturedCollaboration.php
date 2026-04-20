<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class FeaturedCollaboration extends Model
{
    use HasFactory;

    protected $table = 'featured_collaborations';

    protected $fillable = [
        'brand_name',
        'asset_type',
        'image_path',
        'video_path',
        'thumbnail_path',
        'sort_order',
        'is_published'
    ];

    protected function casts(): array
    {
        return [
            'sort_order'   => 'integer',
            'is_published' => 'boolean'
        ];
    }

    /**
     * Scope listing columns for dashboard.
     */
    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'brand_name',
            'asset_type',
            'image_path',
            'video_path',
            'thumbnail_path',
            'sort_order',
            'is_published',
            'updated_at',
        ]);
    }

    /**
     * Scope search by brand name.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        return $query->where('brand_name', 'like', '%' . $term . '%');
    }

    /**
     * Scope dashboard status filter.
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
     * Scope dashboard type filter.
     */
    public function scopeDashboardType(Builder $query, string $type): Builder
    {
        return match ($type) {
            'image' => $query->where('asset_type', 'image'),
            'video' => $query->where('asset_type', 'video'),
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

    /**
     * Get the full URL for image path
     */
    public function getImageUrl(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        return \App\Helpers\ImageHelper::url($this->image_path);
    }

    /**
     * Get the full URL for video path
     */
    public function getVideoUrl(): ?string
    {
        if (!$this->video_path) {
            return null;
        }

        return \App\Helpers\ImageHelper::url($this->video_path);
    }

    /**
     * Get the full URL for thumbnail path
     */
    public function getThumbnailUrl(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }

        return \App\Helpers\ImageHelper::url($this->thumbnail_path);
    }

    /**
     * Get published collaborations ordered by sort_order
     */
    public static function published(): Collection
    {
        return self::where('is_published', true)
            ->orderBy('sort_order', 'asc')
            ->get();
    }
}
