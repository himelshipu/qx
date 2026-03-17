<?php

namespace App\Models;

use App\Models\FaqItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqSection extends Model
{
    protected $fillable = [
        'page_id',
        'section_code',
        'section_title',
        'audience_type',
        'sort_order',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_active'  => 'boolean'
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(FaqItem::class, 'faq_section_id');
    }
}
