<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseStudy extends Model
{
    use SoftDeletes;
    protected $table = 'case_studies';

    protected $fillable = [
        'page_id',
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

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'page_id');
    }

    /**
     * Get published case studies ordered by sort order
     */
    public static function getPublished()
    {
        return self::where('is_published', true)
            ->orderBy('sort_order', 'asc')
            ->orderBy('published_at', 'desc')
            ->get();
    }
}
