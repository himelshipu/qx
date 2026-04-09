<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignAsset extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'asset_type',
        'file_path',
        'mime_type',
        'file_size',
        'title',
        'sort_order'
    ];

    protected function casts(): array
    {
        return [
            'file_size'  => 'integer',
            'sort_order' => 'integer'
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
