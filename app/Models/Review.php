<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'brand_id',
        'influencer_id',
        'reviewer_type',
        'reviewee_type',
        'rating',
        'title',
        'comment',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_public' => 'boolean',
            'reviewer_type' => 'string',
            'reviewee_type' => 'string',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (): void {
            throw new \LogicException('Reviews are immutable once submitted.');
        });

        static::deleting(function (): void {
            throw new \LogicException('Reviews cannot be deleted once submitted.');
        });
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class);
    }
}
