<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BrandBillingProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'legal_company_name',
        'vat_id',
        'billing_address',
        'billing_city',
        'billing_country',
        'billing_postal_code'
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
