<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'name',
        'description',
        'base_price',
        'currency',
        'delivery_days',
        'revisions_included',
        'created_by_user_id',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'base_price'         => 'decimal:2',
            'delivery_days'      => 'integer',
            'revisions_included' => 'integer',
            'is_active'          => 'boolean'
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
