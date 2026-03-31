<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BillingProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_type',
        'legal_company_name',
        'vat_id',
        'billing_address',
        'billing_city',
        'billing_country',
        'billing_postal_code'
    ];

    public function user(): MorphTo
    {
        return $this->morphTo(__FUNCTION__, 'user_type', 'user_id');
    }
}
