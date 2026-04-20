<?php

namespace App\Data\Frontend\Campaign;

use App\Models\Campaign;

class CampaignData
{
    /**
     * Transform campaign model to frontend-ready array structure.
     * This is used by JavaScript/Alpine and template rendering.
     */
    public static function fromModel(Campaign $campaign, ?string $userType = null, ?int $currentUserId = null, ?int $currentBrandId = null): array
    {
        // Resolve campaign image
        $categoryImagePath = $campaign->categories->firstWhere('image_path', '!=', null)?->image_path
            ?? $campaign->categories->first()?->image_path;

        $isExternal = $categoryImagePath
            && (str_starts_with($categoryImagePath, 'http://')
                || str_starts_with($categoryImagePath, 'https://'));

        $image = $categoryImagePath
            ? ($isExternal ? $categoryImagePath : asset($categoryImagePath))
            : asset('images/campaignApply.png');

        // Determine if current user can edit (brand can edit campaigns assigned to them)
        $canEdit = $userType === 'brand' && $campaign->brand_id === $currentBrandId;

        return [
            'id' => $campaign->id,
            'title' => $campaign->title,
            'status' => $campaign->status,
            'campaign_type' => $campaign->campaign_type,
            'is_active' => $campaign->is_active,
            'applications_count' => $campaign->applications_count ?? 0,
            'categories_count' => $campaign->categories_count ?? 0,
            'targeting' => $campaign->targeting ? [
                'influencer_count' => $campaign->targeting->influencer_count ?? 0,
            ] : null,
            'image' => $image,
            'canEdit' => $canEdit,
        ];
    }

    /**
     * Transform collection of campaigns to array structure.
     */
    public static function fromCollection($campaigns, ?string $userType = null, ?int $currentUserId = null, ?int $currentBrandId = null): array
    {
        return $campaigns
            ->map(fn($campaign) => static::fromModel($campaign, $userType, $currentUserId, $currentBrandId))
            ->values()
            ->all();
    }
}
