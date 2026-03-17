<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeaturedCollaboration extends Model
{
    use HasFactory;

    protected $table = 'featured_collaborations';

    protected $fillable = [
        'page_id',
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
     * Get the full URL for image path
     */
    public function getImageUrl(): ?string
    {
        if (!$this->image_path) {
            return null;
        }

        return asset('storage/' . $this->image_path);
    }

    /**
     * Get the full URL for video path
     */
    public function getVideoUrl(): ?string
    {
        if (!$this->video_path) {
            return null;
        }

        return asset('storage/' . $this->video_path);
    }

    /**
     * Get the full URL for thumbnail path
     */
    public function getThumbnailUrl(): ?string
    {
        if (!$this->thumbnail_path) {
            return null;
        }

        return asset('storage/' . $this->thumbnail_path);
    }

    /**
     * Get published collaborations ordered by sort_order
     */
    public static function published()
    {
        return self::where('is_published', true)
            ->orderBy('sort_order', 'asc')
            ->get();
    }
}
