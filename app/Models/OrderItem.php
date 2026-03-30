<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'creator_id',
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
        'approved_at'
    ];

    protected function casts(): array
    {
        return [
            'quantity'     => 'integer',
            'unit_price'   => 'decimal:2',
            'line_total'   => 'decimal:2',
            'due_date'     => 'date',
            'accepted_at'  => 'datetime',
            'delivered_at' => 'datetime',
            'approved_at'  => 'datetime'
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
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

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(OrderDeliverable::class);
    }

    public function payoutItems(): HasMany
    {
        return $this->hasMany(PayoutItem::class);
    }
}
