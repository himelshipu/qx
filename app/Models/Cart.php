<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'expires_at'
    ];

    protected $appends = ['total_price', 'total_items'];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get total price of all items in cart
     */
    public function getTotalPriceAttribute(): float
    {
        return (float) $this->items->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    /**
     * Get total items count in cart
     */
    public function getTotalItemsAttribute(): int
    {
        return (int) $this->items->sum('quantity');
    }
}
