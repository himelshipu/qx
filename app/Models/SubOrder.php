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
        'payout_amount',
        'payout_reference',
        'payout_note',
        'payout_marked_by_user_id',
        'payout_marked_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'accepted_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'paid_at' => 'datetime',
            'payout_amount' => 'decimal:2',
            'payout_marked_at' => 'datetime',
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

    public function payoutMarkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payout_marked_by_user_id');
    }
}
