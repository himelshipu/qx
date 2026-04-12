<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Testimonial extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'page_id',
        'author_name',
        'author_role',
        'company_name',
        'quote',
        'rating',
        'is_published',
        'sort_order'
    ];

    protected function casts(): array
    {
        return [
            'rating'       => 'integer',
            'is_published' => 'boolean',
            'sort_order'   => 'integer'
        ];
    }
}
