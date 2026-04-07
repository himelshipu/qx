<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Influencer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'display_name',
        'title_name',
        'audience',
        'brands_worked_with',
        'is_active',
        'is_featured',
        'featured_priority'
    ];

    protected function casts(): array
    {
        return [
            'is_active'         => 'boolean',
            'is_featured'       => 'boolean',
            'featured_priority' => 'integer',
            'created_at'        => 'datetime',
            'updated_at'        => 'datetime'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function socialLinks(): HasOne
    {
        return $this->hasOne(InfluencerSocialLink::class, 'influencer_id');
    }

    public function platformStats(): HasMany
    {
        return $this->hasMany(InfluencerPlatformStat::class, 'influencer_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'influencer_categories')->withTimestamps();
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(BadgeDefinition::class, 'influencer_badges')->withPivot(['earned_at', 'is_active'])->withTimestamps();
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
        return $this->hasMany(InfluencerPortfolio::class, 'influencer_id')->orderBy('sort_order');
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(Campaign::class, 'campaign_applications')
            ->withPivot(['status', 'pitch_message', 'proposed_rate', 'agreed_rate', 'applied_at', 'decided_at'])
            ->withTimestamps();
    }

    public function payoutAccounts(): HasMany
    {
        return $this->hasMany(PayoutAccount::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function acceptedOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'accepted_for_influencer_id');
    }

    public function billingProfiles(): MorphMany
    {
        return $this->morphMany(BillingProfile::class, 'user', 'user_type', 'user_id');
    }

    /**
     * Get all campaign assignments for this influencer
     */
    public function campaignAssignments(): HasMany
    {
        return $this->hasMany(CampaignInfluencer::class);
    }

    /**
     * Get the active moderator assignment (if any)
     */
    public function activeModerator(): HasMany
    {
        return $this->hasMany(ModeratorAssignment::class)->whereNull('unassigned_at');
    }

    /**
     * Get all moderator assignments history
     */
    public function moderatorAssignments(): HasMany
    {
        return $this->hasMany(ModeratorAssignment::class);
    }

    /**
     * Get all sub-orders for this influencer
     */
    public function subOrders(): HasMany
    {
        return $this->hasMany(SubOrder::class);
    }
}
