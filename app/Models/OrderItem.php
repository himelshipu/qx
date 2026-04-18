<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'influencer_id',
        'package_id',
        'campaign_id',
        'title',
        'description',
        'quantity',
        'unit_price',
        'line_total',
        'status',
        'due_date',
        'accepted_by_user_id',
        'accepted_at',
        'delivered_at',
        'approved_at',
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
            'quantity' => 'integer',
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'due_date' => 'date',
            'accepted_at' => 'datetime',
            'delivered_at' => 'datetime',
            'approved_at' => 'datetime',
            'paid_at' => 'datetime',
            'payout_amount' => 'decimal:2',
            'payout_marked_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class, 'influencer_id');
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }

    public function payoutMarkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payout_marked_by_user_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function brandToInfluencerReview(): HasOne
    {
        return $this->hasOne(Review::class)
            ->where('reviewer_type', 'brand')
            ->where('reviewee_type', 'influencer');
    }

    public function influencerToBrandReview(): HasOne
    {
        return $this->hasOne(Review::class)
            ->where('reviewer_type', 'influencer')
            ->where('reviewee_type', 'brand');
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(OrderDeliverable::class);
    }

    public function payoutItems(): HasMany
    {
        return $this->hasMany(PayoutItem::class);
    }

    public function scopeForPaymentDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'order_id',
            'influencer_id',
            'package_id',
            'title',
            'description',
            'quantity',
            'unit_price',
            'line_total',
            'status',
            'due_date',
            'paid_at',
            'payout_amount',
            'payout_reference',
            'payout_note',
            'payout_marked_by_user_id',
            'payout_marked_at',
            'created_at',
        ]);
    }

    public function scopeUnpaidForDashboard(Builder $query): Builder
    {
        return $query->whereNull('paid_at')->where('status', 'approved');
    }

    public function scopePaidForDashboard(Builder $query): Builder
    {
        return $query->whereNotNull('paid_at');
    }
}
