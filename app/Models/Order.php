<?php

namespace App\Models;

use App\Models\OrderBrandPayment;
use App\Models\OrderMessage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public const SOURCE_PACKAGE  = 'PKG';
    public const SOURCE_CAMPAIGN = 'CMP';
    public const SOURCE_CUSTOM   = 'CUS';

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

    public function brandPayments(): HasMany
    {
        return $this->hasMany(OrderBrandPayment::class);
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

    public function scopeForDashboard(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'order_number',
                'buyer_user_id',
                'brand_id',
                'campaign_id',
                'parent_order_id',
                'status',
                'subtotal',
                'service_fee',
                'tax_amount',
                'total_amount',
                'currency',
                'placed_at',
                'created_at'
            ])
            ->with([
                'buyer:id,name,email,user_type',
                'brand:id,brand_name',
                'campaign:id,title',
                'childOrders:id,parent_order_id,campaign_id,status,total_amount,currency'
            ])
            ->withCount([
                'items as package_items_count'              => fn(Builder $query)              => $query->whereNotNull('package_id'),
                'childOrders as child_orders_count',
                'childOrders as child_package_orders_count' => fn(Builder $query) => $query->whereNull('campaign_id')
            ]);
    }

    public function scopeDashboardSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use ($search): void {
            $subQuery
                ->where('order_number', 'like', '%' . $search . '%')
                ->orWhereHas('buyer', function (Builder $buyerQuery) use ($search): void {
                    $buyerQuery
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                })
                ->orWhereHas('brand', function (Builder $brandQuery) use ($search): void {
                    $brandQuery->where('brand_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('campaign', function (Builder $campaignQuery) use ($search): void {
                    $campaignQuery->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('items', function (Builder $itemQuery) use ($search): void {
                    $itemQuery->where('title', 'like', '%' . $search . '%');
                })
                ->orWhereHas('childOrders.items', function (Builder $itemQuery) use ($search): void {
                    $itemQuery->where('title', 'like', '%' . $search . '%');
                });
        });
    }

    public function scopeDashboardStatus(Builder $query, string $status): Builder
    {
        $status = trim($status);

        return $status === 'all' || $status === ''
        ? $query
        : $query->where('status', $status);
    }

    public function scopeDashboardType(Builder $query, string $type): Builder
    {
        $type = trim($type);

        if ($type === 'campaign') {
            return $query->whereNotNull('campaign_id');
        }

        if ($type === 'package') {
            return $query
                ->whereNull('campaign_id')
                ->where(function (Builder $packageQuery): void {
                    $packageQuery
                        ->whereHas('items', fn(Builder $itemQuery) => $itemQuery->whereNotNull('package_id'))
                        ->orWhereHas('childOrders.items', fn(Builder $itemQuery) => $itemQuery->whereNotNull('package_id'));
                });
        }

        return $query;
    }

    public static function generateOrderNumber(string $source = self::SOURCE_PACKAGE): string
    {
        $normalized = strtoupper(trim($source));

        if (!preg_match('/^[A-Z]{3}$/', $normalized)) {
            $normalized = 'GEN';
        }

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $candidate = sprintf(
                'ROCKIES-%s-%s-%04d',
                $normalized,
                now()->format('ymdHis'),
                random_int(0, 9999)
            );

            if (!static::withTrashed()->where('order_number', $candidate)->exists()) {
                return $candidate;
            }
        }

        return sprintf('ROCKIES-%s-%s', $normalized, strtoupper(uniqid()));
    }
}
