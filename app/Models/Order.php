<?php

namespace App\Models;

use App\Models\OrderMessage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'buyer_user_id',
        'brand_id',
        'campaign_id',
        'parent_order_id',
        'status',
        'accepted_by_user_id',
        'accepted_for_influencer_id',
        'subtotal',
        'service_fee',
        'tax_amount',
        'total_amount',
        'currency',
        'placed_at',
        'accepted_at',
        'completed_at',
        'cancelled_at'
    ];

    protected function casts(): array
    {
        return [
            'subtotal'     => 'decimal:2',
            'service_fee'  => 'decimal:2',
            'tax_amount'   => 'decimal:2',
            'total_amount' => 'decimal:2',
            'placed_at'    => 'datetime',
            'accepted_at'  => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime'
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_user_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function parentOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'parent_order_id');
    }

    public function childOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'parent_order_id');
    }

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }

    public function acceptedForInfluencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class, 'accepted_for_influencer_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(OrderMessage::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }

    /**
     * Get all sub-orders for this master order
     */
    public function subOrders(): HasMany
    {
        return $this->hasMany(SubOrder::class);
    }

    /**
     * Check if this is a master order (has sub-orders)
     */
    public function isMasterOrder(): bool
    {
        return $this->subOrders()->count() > 0;
    }

    public function isParentOrder(): bool
    {
        return $this->parent_order_id === null;
    }
}
