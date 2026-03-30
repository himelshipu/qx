<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayoutAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'creator_id',
        'provider',
        'account_identifier',
        'account_name',
        'is_default',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'is_active'  => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime'
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }
}
