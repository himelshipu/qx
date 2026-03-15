<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignTargeting extends Model
{
    use HasFactory;

    protected $table = 'campaign_targeting';

    protected $fillable = [
        'campaign_id',
        'influencer_count',
        'target_gender',
        'age_min',
        'age_max',
        'notes'
    ];

    protected function casts(): array
    {
        return [
            'influencer_count' => 'integer',
            'age_min'          => 'integer',
            'age_max'          => 'integer'
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
