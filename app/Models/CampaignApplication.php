<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'influencer_id',
        'status',
        'pitch_message',
        'proposed_rate',
        'agreed_rate',
        'applied_at',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'proposed_rate' => 'decimal:2',
            'agreed_rate' => 'decimal:2',
            'applied_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }
}
