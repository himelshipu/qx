<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'influencer_id',
        'platform',
        'name',
        'description',
        'base_price',
        'currency',
        'delivery_days',
        'revisions_included',
        'created_by',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'base_price'         => 'decimal:2',
            'delivery_days'      => 'integer',
            'revisions_included' => 'integer',
            'is_active'          => 'boolean',
            'created_at'         => 'datetime',
            'updated_at'         => 'datetime'
        ];
    }

    public function influencer(): BelongsTo
    {
        return $this->belongsTo(Influencer::class, 'influencer_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function orders(): HasManyThrough
    {
        return $this->hasManyThrough(Order::class, OrderItem::class, 'package_id', 'id', 'id', 'order_id');
    }

    public function scopeForDashboard(Builder $query): Builder
    {
        return $query
            ->select([
                'id',
                'created_by',
                'platform',
                'name',
                'description',
                'base_price',
                'currency',
                'delivery_days',
                'revisions_included',
                'is_active',
                'updated_at',
            ])
            ->with('createdBy:id,name')
            ->withCount(['cartItems', 'orderItems']);
    }

    public function scopeDashboardSearch(Builder $query, string $search): Builder
    {
        $search = trim($search);

        if ($search === '') {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use ($search): void {
            $subQuery
                ->where('name', 'like', '%' . $search . '%')
                ->orWhere('platform', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%')
                ->orWhere('currency', 'like', '%' . $search . '%');
        });
    }

    public function scopeDashboardStatus(Builder $query, ?string $status): Builder
    {
        $normalized = strtolower(trim((string) $status));

        return match ($normalized) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            default => $query,
        };
    }

    public function scopeDashboardPlatform(Builder $query, ?string $platform): Builder
    {
        $platform = strtolower(trim((string) $platform));

        if ($platform === '' || $platform === 'all') {
            return $query;
        }

        return $query->where('platform', $platform);
    }

    public function scopeDashboardOrder(Builder $query): Builder
    {
        return $query->orderByDesc('updated_at');
    }
}
