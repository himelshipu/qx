<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Campaign extends Model
{
    use HasFactory, SoftDeletes;

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
            ->withPivot([
                'status',
                'pitch_message',
                'influencer_offer',
                'brand_offer',
                'proposed_rate',
                'agreed_rate',
                'last_counter_by',
                'last_counter_at',
                'agreed_at',
                'declined_at',
                'declined_by',
                'applied_at',
                'decided_at',
            ])
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

    /**
     * Scope a query for dashboard list payload.
     */
    public function scopeForDashboard(Builder $query): Builder
    {
        return $query->select([
            'id',
            'brand_id',
            'title',
            'campaign_type',
            'description',
            'status',
            'budget_min',
            'budget_max',
            'currency',
            'is_active',
            'updated_at',
        ])->with([
            'brand:id,brand_name,user_id',
            'targeting:id,campaign_id,influencer_count',
            'categories:id,name,image_path',
        ])->withCount([
            'categories',
            'applications',
            'orders',
            'orderItems',
            'cartItems',
        ]);
    }

    /**
     * Scope a query by optional brand.
     */
    public function scopeDashboardBrand(Builder $query, ?int $brandId = null): Builder
    {
        return $brandId !== null ? $query->where('brand_id', $brandId) : $query;
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
                ->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('instructions', 'like', "%{$term}%")
                ->orWhere('campaign_type', 'like', "%{$term}%")
                ->orWhere('status', 'like', "%{$term}%");
        });
    }

    /**
     * Scope a query by dashboard status filter.
     */
    public function scopeDashboardStatus(Builder $query, string $status): Builder
    {
        return $status !== 'all' ? $query->where('status', $status) : $query;
    }

    /**
     * Scope a query by dashboard campaign type filter.
     */
    public function scopeDashboardType(Builder $query, string $type): Builder
    {
        return $type !== 'all' ? $query->where('campaign_type', $type) : $query;
    }

    /**
     * Scope a query ordered for dashboard listing.
     */
    public function scopeDashboardOrder(Builder $query): Builder
    {
        return $query->orderByDesc('updated_at');
    }

    /**
     * Short description label for dashboard rows.
     */
    public function getDashboardDescriptionAttribute(): string
    {
        return Str::limit($this->description ?? 'No description provided.', 70);
    }

    /**
     * Status badge style class for dashboard rows.
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ((string) $this->status) {
            'published' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
            'paused' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'closed' => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'archived' => 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
            default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        };
    }

    /**
     * Single status label used in dashboard list.
     */
    public function getDashboardStatusLabelAttribute(): string
    {
        $lifecycle = Str::headline((string) $this->status);
        $activity = $this->is_active ? 'Active' : 'Inactive';

        return $lifecycle . ' / ' . $activity;
    }

    /**
     * Single badge style used in dashboard list.
     */
    public function getDashboardStatusBadgeClassAttribute(): string
    {
        $baseClass = $this->status_badge_class;

        if ($this->is_active) {
            return $baseClass;
        }

        return $baseClass . ' opacity-80 ring-1 ring-inset ring-gray-300/70 dark:ring-gray-600/70';
    }

    /**
     * Formatted budget range for dashboard rows.
     */
    public function getBudgetRangeLabelAttribute(): string
    {
        if ($this->budget_min === null && $this->budget_max === null) {
            return 'Not set';
        }

        $minBudget = $this->budget_min !== null
            ? $this->currency . ' ' . number_format((float) $this->budget_min, 2)
            : 'N/A';

        $maxBudget = $this->budget_max !== null
            ? $this->currency . ' ' . number_format((float) $this->budget_max, 2)
            : 'N/A';

        return $minBudget . ' - ' . $maxBudget;
    }

    /**
     * Total linked dependency count used for delete guard.
     */
    public function getDependencyCountAttribute(): int
    {
        return (int) ($this->applications_count ?? 0)
            + (int) ($this->orders_count ?? 0)
            + (int) ($this->order_items_count ?? 0)
            + (int) ($this->cart_items_count ?? 0);
    }
}
