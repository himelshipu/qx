<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_id',
        'influencer_id',
        'status',
        'work_status',
        'pitch_message',
        'influencer_offer',
        'brand_offer',
        'proposed_rate',
        'agreed_rate',
        'last_counter_by',
        'last_counter_at',
        'agreed_at',
        'declined_at',
        'declined_by',
        'applied_at',
        'decided_at',
    ];

    protected function casts(): array
    {
        return [
            'influencer_offer' => 'decimal:2',
            'brand_offer' => 'decimal:2',
            'proposed_rate' => 'decimal:2',
            'agreed_rate' => 'decimal:2',
            'last_counter_at' => 'datetime',
            'agreed_at' => 'datetime',
            'declined_at' => 'datetime',
            'applied_at' => 'datetime',
            'decided_at' => 'datetime',
        ];
    }

    public function canNegotiate(): bool
    {
        return in_array((string) $this->status, [
            'invited',
            'applied',
            'countered_by_brand',
            'countered_by_influencer',
        ], true);
    }

    public function isTerminal(): bool
    {
        return in_array((string) $this->status, [
            'approved',
            'completed',
            'rejected',
            'declined_by_brand',
            'declined_by_influencer',
        ], true);
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
