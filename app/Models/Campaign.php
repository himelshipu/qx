<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_id',
        'created_by',
        'title',
        'campaign_type',
        'description',
        'instructions',
        'status',
        'budget_min',
        'budget_max',
        'currency',
        'start_date',
        'end_date',
        'published_at',
        'is_active'
    ];

    protected function casts(): array
    {
        return [
            'budget_min'   => 'decimal:2',
            'budget_max'   => 'decimal:2',
            'start_date'   => 'date',
            'end_date'     => 'date',
            'published_at' => 'datetime',
            'is_active'    => 'boolean'
        ];
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function targeting(): HasOne
    {
        return $this->hasOne(CampaignTargeting::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'campaign_categories')->withTimestamps();
    }

    public function followerRanges(): BelongsToMany
    {
        return $this->belongsToMany(FollowerRange::class, 'campaign_target_follower_ranges')->withTimestamps();
    }

    public function assets(): HasMany
    {
        return $this->hasMany(CampaignAsset::class);
    }

    public function targetCountries(): HasMany
    {
        return $this->hasMany(CampaignTargetCountry::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(CampaignApplication::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function influencers(): BelongsToMany
    {
        return $this->belongsToMany(Influencer::class, 'campaign_applications')
            ->withPivot(['status', 'pitch_message', 'proposed_rate', 'agreed_rate', 'applied_at', 'decided_at'])
            ->withTimestamps();
    }

    /**
     * Get all influencer assignments for this campaign
     */
    public function influencerAssignments(): HasMany
    {
        return $this->hasMany(CampaignInfluencer::class);
    }

    /**
     * Get all approved influencers for this campaign
     */
    public function approvedInfluencers(): HasMany
    {
        return $this->hasMany(CampaignInfluencer::class)->where('status', 'approved');
    }
}
