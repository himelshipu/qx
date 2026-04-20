<?php

namespace App\ViewModels\Frontend\Campaign;

use App\Data\Frontend\Campaign\CampaignData;
use App\Models\User;
use Illuminate\Contracts\Pagination\Paginator;

class CampaignIndexViewModel
{
    public function __construct(
        protected Paginator $campaigns,
        protected User $user,
        protected string $search,
        protected string $status,
        protected string $type,
    ) {}

    /**
     * Get filter options for status
     */
    public function statusOptions(): array
    {
        return [
            ['value' => 'all', 'label' => 'All Status'],
            ['value' => 'draft', 'label' => 'Draft'],
            ['value' => 'published', 'label' => 'Published'],
            ['value' => 'paused', 'label' => 'Paused'],
            ['value' => 'closed', 'label' => 'Closed'],
            ['value' => 'archived', 'label' => 'Archived']
        ];
    }

    /**
     * Get filter options for campaign type
     */
    public function typeOptions(): array
    {
        return [
            ['value' => 'all', 'label' => 'All Types'],
            ['value' => 'facebook', 'label' => 'Facebook'],
            ['value' => 'instagram', 'label' => 'Instagram'],
            ['value' => 'tiktok', 'label' => 'TikTok'],
            ['value' => 'linkedin', 'label' => 'LinkedIn'],
            ['value' => 'x', 'label' => 'X'],
            ['value' => 'youtube', 'label' => 'YouTube'],
            ['value' => 'ugc', 'label' => 'UGC'],
            ['value' => 'other', 'label' => 'Other']
        ];
    }

    /**
     * Get campaigns data transformed for frontend
     */
    public function campaignsData(): array
    {
        return CampaignData::fromCollection(
            $this->campaigns,
            $this->user->user_type,
            $this->user->id,
            $this->user->brand?->id
        );
    }

    /**
     * Get total number of campaigns
     */
    public function total(): int
    {
        return $this->campaigns->total();
    }

    /**
     * Get current search query
     */
    public function search(): string
    {
        return $this->search;
    }

    /**
     * Get current status filter
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * Get current type filter
     */
    public function type(): string
    {
        return $this->type;
    }

    /**
     * Get user type (brand or influencer)
     */
    public function userType(): string
    {
        return $this->user->user_type;
    }

    /**
     * Check if user is brand
     */
    public function isBrand(): bool
    {
        return $this->user->user_type === 'brand';
    }

    /**
     * Check if user is influencer
     */
    public function isInfluencer(): bool
    {
        return $this->user->user_type === 'influencer';
    }

    /**
     * Get all view data as array for template
     */
    public function toArray(): array
    {
        return [
            'campaigns' => $this->campaigns,
            'campaignsData' => $this->campaignsData(),
            'userType' => $this->userType(),
            'search' => $this->search(),
            'status' => $this->status(),
            'type' => $this->type(),
            'statusOptions' => $this->statusOptions(),
            'typeOptions' => $this->typeOptions(),
        ];
    }
}
