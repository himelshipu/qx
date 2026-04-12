<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // Base identity and taxonomy
            \Database\Seeders\UserSeeder::class,
            \Database\Seeders\RoleSeeder::class,
            \Database\Seeders\PermissionSeeder::class,
            \Database\Seeders\RolePermissionSeeder::class,
            \Database\Seeders\UserRoleSeeder::class,
            \Database\Seeders\CategorySeeder::class,
            \Database\Seeders\FollowerRangeSeeder::class,

            // Brand and influencer profiles
            \Database\Seeders\BrandSeeder::class,
            \Database\Seeders\BrandSocialLinkSeeder::class,
            \Database\Seeders\BillingProfileSeeder::class,
            \Database\Seeders\BrandOnboardingProfileSeeder::class,
            \Database\Seeders\BrandOnboardingIndustrySeeder::class,
            \Database\Seeders\InfluencerSeeder::class,
            \Database\Seeders\InfluencerSocialLinkSeeder::class,
            \Database\Seeders\InfluencerPlatformStatSeeder::class,
            \Database\Seeders\InfluencerCategorySeeder::class,
            \Database\Seeders\BadgeDefinitionSeeder::class,
            \Database\Seeders\InfluencerBadgeSeeder::class,
            \Database\Seeders\InfluencerPortfolioSeeder::class,

            // Campaign and package commerce sources
            \Database\Seeders\CampaignSeeder::class,
            \Database\Seeders\CampaignTargetingSeeder::class,
            \Database\Seeders\CampaignCategorySeeder::class,
            \Database\Seeders\CampaignTargetCountrySeeder::class,
            \Database\Seeders\CampaignTargetFollowerRangeSeeder::class,
            \Database\Seeders\CampaignAssetSeeder::class,
            \Database\Seeders\CampaignApplicationSeeder::class,
            \Database\Seeders\PackageSeeder::class,

            // Orders and reviews
            \Database\Seeders\OrderSeeder::class,
            \Database\Seeders\OrderItemSeeder::class,
            \Database\Seeders\ReviewSeeder::class,

            // Support flow
            \Database\Seeders\SupportCategorySeeder::class,
            \Database\Seeders\SupportTicketSeeder::class,
            \Database\Seeders\SupportTicketMessageSeeder::class,
            \Database\Seeders\SupportTicketAttachmentSeeder::class,

            // Static content blocks
            \Database\Seeders\CaseStudySeeder::class,
            \Database\Seeders\TestimonialSeeder::class,
            \Database\Seeders\FaqSectionSeeder::class,
            \Database\Seeders\FaqItemSeeder::class,
            \Database\Seeders\KnowledgeBaseSeeder::class,
            \Database\Seeders\FeaturedCollaborationSeeder::class,

            // Admin dashboard configuration
            \Database\Seeders\AdminMenuSeeder::class,
        ]);
    }
}
