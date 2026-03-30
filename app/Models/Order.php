<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'buyer_user_id',
        'brand_id',
        'campaign_id',
        'status',
        'accepted_by_user_id',
        'accepted_for_creator_id',
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

    public function acceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'accepted_by_user_id');
    }

    public function acceptedForCreator(): BelongsTo
    {
        return $this->belongsTo(Creator::class, 'accepted_for_creator_id');
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
}
