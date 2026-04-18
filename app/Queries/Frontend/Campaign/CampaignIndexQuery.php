<?php

namespace App\Queries\Frontend\Campaign;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;

class CampaignIndexQuery
{
    protected User $user;
    protected string $search = '';
    protected string $status = 'all';
    protected string $type = 'all';
    protected int $perPage = 12;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Set search query
     */
    public function withSearch(string $search): self
    {
        $this->search = trim($search);
        return $this;
    }

    /**
     * Set status filter
     */
    public function withStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Set type filter
     */
    public function withType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Set items per page
     */
    public function withPerPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }

    /**
     * Get paginated results based on user role
     */
    public function paginate(): Paginator
    {
        return $this->buildQuery()
            ->paginate($this->perPage)
            ->withQueryString();
    }

    /**
     * Build the query based on user role and filters
     */
    protected function buildQuery(): Builder
    {
        $query = Campaign::query()->select([
            'id',
            'title',
            'campaign_type',
            'status',
            'is_active',
            'currency',
            'start_date',
            'end_date',
            'created_by',
            'brand_id',
            'created_at',
        ]);

        // Role-based filtering
        if ($this->user->user_type === 'brand') {
            // Brands see campaigns they created OR campaigns assigned to them (including by admin)
            $brandId = $this->user->brand?->id;
            $query->where(function (Builder $q) {
                $q->where('created_by', $this->user->id)
                    ->orWhere('brand_id', $this->user->brand?->id);
            });

            if ($brandId === null) {
                $query->where('created_by', $this->user->id);
            }
        } elseif ($this->user->user_type === 'influencer') {
            // Influencers see campaigns they've applied to
            $influencerId = $this->user->influencer?->id;
            if (! $influencerId) {
                $query->whereRaw('1 = 0');

                return $query;
            }

            $query->whereHas('applications', function (Builder $q) {
                $q->where('influencer_id', $this->user->influencer->id);
            });
        }

        // Apply common filters
        $query->where('is_active', true);

        // Search filter
        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%");
        }

        // Status filter
        if ($this->status !== 'all') {
            $query->where('status', $this->status);
        }

        // Type filter
        if ($this->type !== 'all') {
            $query->where('campaign_type', $this->type);
        }

        // Eager load relationships to prevent N+1 queries
        $query->with([
            'categories' => fn ($q) => $q->select('categories.id', 'categories.image_path'),
            'targeting' => fn ($q) => $q->select('id', 'campaign_id', 'influencer_count'),
        ])
        ->withCount(['applications', 'categories']);

        // Order by creation date
        $query->orderByDesc('created_at');

        return $query;
    }
}
