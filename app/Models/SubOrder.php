<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'campaign_influencer_id',
        'influencer_id',
        'status',
        'deliverables',
        'amount',
        'currency',
        'accepted_at',
        'completed_at',
        'cancelled_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the parent master order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the campaign influencer assignment
     */
    public function campaignInfluencer(): BelongsTo
    {
        return $this->belongsTo(CampaignInfluencer::class);
    }

    /**
     * Get the influencer for this sub-order
     */
    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }
}
