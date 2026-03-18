<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Creator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'title_name',
        'description',
        'audience',
        'brands_worked_with',
        'location',
        'city',
        'country',
        'postal_code',
        'gender',
        'profile_image_path',
        'cover_image_path',
        'is_active',
        'is_featured',
        'featured_priority'
    ];

    protected function casts(): array
    {
        return [
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'featured_priority' => 'integer'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function socialLinks(): HasOne
    {
        return $this->hasOne(CreatorSocialLink::class);
    }

    public function platformStats(): HasMany
    {
        return $this->hasMany(CreatorPlatformStat::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'creator_categories')->withTimestamps();
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(BadgeDefinition::class, 'creator_badges')->withPivot(['earned_at', 'is_active'])->withTimestamps();
    }

    public function campaignApplications(): HasMany
    {
        return $this->hasMany(CampaignApplication::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(CreatorPortfolio::class)->orderBy('sort_order');
    }
}
