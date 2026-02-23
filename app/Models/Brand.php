<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    /** @use HasFactory<\Database\Factories\BrandFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'brand_name',
        'description',
        'website',
        'phone',
        'email',
        'location',
        'city',
        'country',
        'postal_code',
        'profile_image_path',
        'cover_image_path',
        'categories',
        'social_links',
        'setup_data',
        'is_verified',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'setup_data' => 'json',
            'categories' => 'json',
            'social_links' => 'json',
        ];
    }

    /**
     * Get the user that owns the brand.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
