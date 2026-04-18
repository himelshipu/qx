<?php

declare (strict_types = 1);

namespace App\Providers;

use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\CampaignRepositoryInterface;
use App\Repositories\Contracts\CommunicationBadgeRepositoryInterface;
use App\Repositories\Contracts\CaseStudyRepositoryInterface;
use App\Repositories\Contracts\ConversationRepositoryInterface;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use App\Repositories\Contracts\SupportTicketRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\FeaturedCollaborationRepositoryInterface;
use App\Repositories\Contracts\FaqItemRepositoryInterface;
use App\Repositories\Contracts\FaqSectionRepositoryInterface;
use App\Repositories\Contracts\InfluencerRepositoryInterface;
use App\Repositories\Contracts\KnowledgeBaseArticleRepositoryInterface;
use App\Repositories\Contracts\ModeratorRepositoryInterface;
use App\Repositories\Contracts\PackageRepositoryInterface;
use App\Repositories\Contracts\PermissionRepositoryInterface;
use App\Repositories\Contracts\RoleRepositoryInterface;
use App\Repositories\Contracts\SettingRepositoryInterface;
use App\Repositories\Contracts\StaticPageRepositoryInterface;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\Eloquent\EloquentBrandRepository;
use App\Repositories\Eloquent\EloquentCampaignRepository;
use App\Repositories\Eloquent\EloquentCommunicationBadgeRepository;
use App\Repositories\Eloquent\EloquentCaseStudyRepository;
use App\Repositories\Eloquent\EloquentConversationRepository;
use App\Repositories\Eloquent\EloquentNotificationRepository;
use App\Repositories\Eloquent\EloquentSupportTicketRepository;
use App\Repositories\Eloquent\EloquentCategoryRepository;
use App\Repositories\Eloquent\EloquentFeaturedCollaborationRepository;
use App\Repositories\Eloquent\EloquentFaqItemRepository;
use App\Repositories\Eloquent\EloquentFaqSectionRepository;
use App\Repositories\Eloquent\EloquentInfluencerRepository;
use App\Repositories\Eloquent\EloquentKnowledgeBaseArticleRepository;
use App\Repositories\Eloquent\EloquentModeratorRepository;
use App\Repositories\Eloquent\EloquentPackageRepository;
use App\Repositories\Eloquent\EloquentPermissionRepository;
use App\Repositories\Eloquent\EloquentRoleRepository;
use App\Repositories\Eloquent\EloquentSettingRepository;
use App\Repositories\Eloquent\EloquentStaticPageRepository;
use App\Repositories\Eloquent\EloquentTestimonialRepository;
use App\Repositories\Eloquent\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 *
 * Registers repository interfaces to their Eloquent implementations.
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryInterface::class,
            EloquentUserRepository::class
        );

        $this->app->bind(
            CategoryRepositoryInterface::class,
            EloquentCategoryRepository::class
        );

        $this->app->bind(
            BrandRepositoryInterface::class,
            EloquentBrandRepository::class
        );

        $this->app->bind(
            InfluencerRepositoryInterface::class,
            EloquentInfluencerRepository::class
        );

        $this->app->bind(
            KnowledgeBaseArticleRepositoryInterface::class,
            EloquentKnowledgeBaseArticleRepository::class
        );

        $this->app->bind(
            CampaignRepositoryInterface::class,
            EloquentCampaignRepository::class
        );

        $this->app->bind(
            CaseStudyRepositoryInterface::class,
            EloquentCaseStudyRepository::class
        );

        $this->app->bind(
            FeaturedCollaborationRepositoryInterface::class,
            EloquentFeaturedCollaborationRepository::class
        );

        $this->app->bind(
            FaqSectionRepositoryInterface::class,
            EloquentFaqSectionRepository::class
        );

        $this->app->bind(
            FaqItemRepositoryInterface::class,
            EloquentFaqItemRepository::class
        );

        $this->app->bind(
            ModeratorRepositoryInterface::class,
            EloquentModeratorRepository::class
        );

        $this->app->bind(
            PackageRepositoryInterface::class,
            EloquentPackageRepository::class
        );

        $this->app->bind(
            RoleRepositoryInterface::class,
            EloquentRoleRepository::class
        );

        $this->app->bind(
            PermissionRepositoryInterface::class,
            EloquentPermissionRepository::class
        );

        $this->app->bind(
            TestimonialRepositoryInterface::class,
            EloquentTestimonialRepository::class
        );

        $this->app->bind(
            SettingRepositoryInterface::class,
            EloquentSettingRepository::class
        );

        $this->app->bind(
            CommunicationBadgeRepositoryInterface::class,
            EloquentCommunicationBadgeRepository::class
        );

        $this->app->bind(
            ConversationRepositoryInterface::class,
            EloquentConversationRepository::class
        );

        $this->app->bind(
            SupportTicketRepositoryInterface::class,
            EloquentSupportTicketRepository::class
        );

        $this->app->bind(
            NotificationRepositoryInterface::class,
            EloquentNotificationRepository::class
        );

        $this->app->bind(
            StaticPageRepositoryInterface::class,
            EloquentStaticPageRepository::class
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
