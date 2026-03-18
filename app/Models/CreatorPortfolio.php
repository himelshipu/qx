<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorPortfolio extends Model
{
    protected $fillable = [
        'creator_id',
        'media_type',
        'file_path',
        'title',
        'description',
        'sort_order',
        'is_active'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'is_active'  => 'boolean'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }
}
