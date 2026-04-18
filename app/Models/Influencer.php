<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Influencer extends Model
{
    use HasFactory, SoftDeletes;

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

    public function orders(): HasMany
    {
        return $this->acceptedOrders();
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

    /**
     * Scope a query for dashboard list payload.
     */
    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'user_id',
            'display_name',
            'title_name',
            'audience',
            'is_active',
            'is_featured',
            'featured_priority',
            'updated_at',
        ])->with([
            'user:id,name,email,profile_image_path,cover_image_path',
            'categories:id,name',
        ])->withCount([
            'categories',
            'campaignApplications',
            'orderItems',
        ]);
    }

    /**
     * Scope a query by dashboard search term.
     */
    public function scopeSearchDashboard(Builder $query, string $search): Builder
    {
        $term = trim($search);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $subQuery) use ($term): void {
            $subQuery
                ->where('display_name', 'like', "%{$term}%")
                ->orWhere('title_name', 'like', "%{$term}%")
                ->orWhereHas('user', function (Builder $userQuery) use ($term): void {
                    $userQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('city', 'like', "%{$term}%")
                        ->orWhere('country', 'like', "%{$term}%");
                })
                ->orWhereHas('categories', function (Builder $categoryQuery) use ($term): void {
                    $categoryQuery->where('name', 'like', "%{$term}%");
                });
        });
    }

    /**
     * Scope a query by dashboard status filter.
     */
    public function scopeFilterStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            'featured' => $query->where('is_featured', true),
            default => $query,
        };
    }

    /**
     * Scope a query by featured filter.
     */
    public function scopeDashboardFeatured(Builder $query, string $featured): Builder
    {
        return match ($featured) {
            'featured' => $query->where('is_featured', true),
            'non-featured' => $query->where('is_featured', false),
            default => $query,
        };
    }

    /**
     * Scope a query to dashboard default ordering.
     */
    public function scopeDashboardOrder(Builder $query): Builder
    {
        return $query
            ->orderByDesc('updated_at');
    }
}
