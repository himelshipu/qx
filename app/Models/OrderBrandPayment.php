<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderBrandPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'brand_user_id',
        'brand_id',
        'amount',
        'currency',
        'payment_method',
        'reference_number',
        'invoice_id',
        'brand_note',
        'admin_note',
        'status',
        'submitted_at',
        'confirmed_at',
        'rejected_at',
        'confirmed_by_user_id',
        'rejected_by_user_id',
        'paypal_token',
        'paypal_transaction_id',
        'paypal_order_id',
        'refunded_at'
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'submitted_at' => 'datetime',
            'confirmed_at' => 'datetime',
            'rejected_at'  => 'datetime',
            'refunded_at'  => 'datetime',
            'created_at'   => 'datetime',
            'updated_at'   => 'datetime'
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function brandUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'brand_user_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by_user_id');
    }

    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by_user_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', 'rejected');
    }

    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'order_id',
            'brand_user_id',
            'brand_id',
            'amount',
            'currency',
            'reference_number',
            'invoice_id',
            'brand_note',
            'admin_note',
            'status',
            'submitted_at',
            'confirmed_at',
            'rejected_at',
            'confirmed_by_user_id',
            'rejected_by_user_id',
            'created_at'
        ]);
    }
}
