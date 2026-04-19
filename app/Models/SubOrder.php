<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'approved_at',
        'completed_at',
        'cancelled_at',
        'paid_at',
        'payout_amount',
        'payout_reference',
        'payout_note',
        'payout_marked_by_user_id',
        'payout_marked_at'
    ];

    protected function casts(): array
    {
        return [
            'amount'           => 'decimal:2',
            'accepted_at'      => 'datetime',
            'approved_at'      => 'datetime',
            'completed_at'     => 'datetime',
            'cancelled_at'     => 'datetime',
            'paid_at'          => 'datetime',
            'payout_amount'    => 'decimal:2',
            'payout_marked_at' => 'datetime'
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

    /**
     * Get the deliverables submitted for this sub-order (campaign order)
     */
    public function deliverables(): HasMany
    {
        return $this->hasMany(OrderDeliverable::class);
    }

    /**
     * Get the review submitted for this campaign order
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'sub_order_id', 'id');
    }

    public function scopeForPaymentDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'order_id',
            'campaign_influencer_id',
            'influencer_id',
            'status',
            'deliverables',
            'amount',
            'currency',
            'accepted_at',
            'completed_at',
            'paid_at',
            'payout_amount',
            'payout_reference',
            'payout_note',
            'payout_marked_by_user_id',
            'payout_marked_at',
            'created_at'
        ]);
    }

    public function scopeUnpaidForDashboard(Builder $query): Builder
    {
        return $query->whereNull('paid_at')->where('status', 'completed');
    }

    public function scopePaidForDashboard(Builder $query): Builder
    {
        return $query->whereNotNull('paid_at');
    }
}
