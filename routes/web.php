<?php

declare (strict_types = 1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\CampaignController;
use App\Http\Controllers\Backend\CaseStudyController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\CreatorController;
use App\Http\Controllers\Backend\CreatorPortfolioController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\FeaturedCollaborationController;
use App\Http\Controllers\Backend\KnowledgeBaseController;
use App\Http\Controllers\Backend\ModeratorController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\PackageController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ReviewController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SupportTicketController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\BrandProfileController;
use App\Http\Controllers\Frontend\ContentLibraryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\InfluencersController;
use App\Http\Controllers\Frontend\KnowledgeBaseController as FrontendKnowledgeBaseController;
use App\Http\Controllers\Frontend\StaticPagesController;
use App\Http\Controllers\Frontend\SupportTicketController as FrontendSupportTicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
 */
Route::middleware(['web'])->group(function () {
    // Home page
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/campaigns', [HomeController::class, 'campaigns'])->name('campaigns');
    Route::get('/faq', [StaticPagesController::class, 'faq'])->name('faq');
    Route::get('/knowledge-base', [FrontendKnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
    Route::get('/knowledge-base/{article:slug}', [FrontendKnowledgeBaseController::class, 'show'])->name('knowledge-base.show');
    Route::get('/support', [FrontendSupportTicketController::class, 'index'])->name('support');
    Route::post('/support-tickets', [FrontendSupportTicketController::class, 'store'])->name('support-tickets.store');

    // Public profile pages
    Route::get('/creator/{slug}', [\App\Http\Controllers\CreatorProfileController::class, 'show'])->name('creator.profile');
    Route::get('/brand/{slug}', [\App\Http\Controllers\BrandProfileController::class, 'show'])->name('brand.profile');

    // Influencers pages
    Route::get('/influencers', [InfluencersController::class, 'index'])->name('influencers');
    Route::get('/influencer/{platformSlug}', [InfluencersController::class, 'index'])->name('influencers.platform');
    Route::get('/category/{categorySlug}', [InfluencersController::class, 'byCategory'])->name('influencers.category');
    Route::get('/ugc', [InfluencersController::class, 'ugc'])->name('influencers.ugc');

    // API endpoints
    Route::get('/api/categories', [InfluencersController::class, 'apiCategories']);
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Breeze)
|--------------------------------------------------------------------------
 */
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'verified'])->group(function () {

    // Assign creators to campaigns
    Route::get('/campaigns/assign', [CampaignController::class, 'assign'])->name('campaigns.assign');
    Route::post('/campaigns/assign', [CampaignController::class, 'assignStore'])->name('campaigns.assign.store');
    // AJAX: Get assigned creators for a campaign
    Route::get('/campaigns/{campaign}/assigned-creators', [CampaignController::class, 'assignedCreatorsJson'])->name('campaigns.assigned-creators');
    // Dashboard

    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/details/{brand}', [BrandController::class, 'view'])->name('brands.view');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
    Route::post('/brands/{brand}/toggle-status', [BrandController::class, 'toggleStatus'])->name('brands.toggle-status');

    Route::get('/creators', [CreatorController::class, 'index'])->name('creators.index');
    Route::get('/creators/create', [CreatorController::class, 'create'])->name('creators.create');
    Route::post('/creators', [CreatorController::class, 'store'])->name('creators.store');
    Route::get('/creators/details/{creator}', [CreatorController::class, 'view'])->name('creators.view');
    Route::get('/creators/{creator}/edit', [CreatorController::class, 'edit'])->name('creators.edit');
    Route::put('/creators/{creator}', [CreatorController::class, 'update'])->name('creators.update');
    Route::delete('/creators/{creator}', [CreatorController::class, 'destroy'])->name('creators.destroy');
    Route::post('/creators/{creator}/toggle-status', [CreatorController::class, 'toggleStatus'])->name('creators.toggle-status');
    Route::post('/creators/{creator}/toggle-featured', [CreatorController::class, 'toggleFeatured'])->name('creators.toggle-featured');

    // Creator Portfolio Management
    Route::get('/creators/{creator}/portfolio', [CreatorPortfolioController::class, 'index'])->name('creators.portfolio.index');
    Route::get('/creators/{creator}/portfolio/create', [CreatorPortfolioController::class, 'create'])->name('creators.portfolio.create');
    Route::post('/creators/{creator}/portfolio', [CreatorPortfolioController::class, 'store'])->name('creators.portfolio.store');
    Route::get('/creators/{creator}/portfolio/{portfolio}/edit', [CreatorPortfolioController::class, 'edit'])->name('creators.portfolio.edit');
    Route::put('/creators/{creator}/portfolio/{portfolio}', [CreatorPortfolioController::class, 'update'])->name('creators.portfolio.update');
    Route::delete('/creators/{creator}/portfolio/{portfolio}', [CreatorPortfolioController::class, 'destroy'])->name('creators.portfolio.destroy');
    Route::post('/creators/{creator}/portfolio/reorder', [CreatorPortfolioController::class, 'reorder'])->name('creators.portfolio.reorder');
    Route::post('/creators/{creator}/portfolio/{portfolio}/toggle', [CreatorPortfolioController::class, 'toggle'])->name('creators.portfolio.toggle');

    Route::get('/campaigns', [CampaignController::class, 'indexDesigned'])->name('campaigns.index');
    Route::get('/campaigns/designed', [CampaignController::class, 'indexDesigned'])->name('campaigns.designed');
    Route::get('/campaigns/standard', [CampaignController::class, 'index'])->name('campaigns.standard');
    Route::get('/campaigns/create', [CampaignController::class, 'createDesigned'])->name('campaigns.create');
    Route::get('/campaigns/designed/create', [CampaignController::class, 'createDesigned'])->name('campaigns.designed.create');
    Route::get('/campaigns/standard/create', [CampaignController::class, 'create'])->name('campaigns.standard.create');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/details/{campaign}', [CampaignController::class, 'view'])->name('campaigns.view');
    Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');

    // Commerce and operations modules
    Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::post('/reviews/{review}/toggle-visibility', [ReviewController::class, 'toggleVisibility'])->name('reviews.toggle-visibility');
    Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('/packages/create', [PackageController::class, 'create'])->name('packages.create');
    Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
    Route::get('/packages/purchase', [PackageController::class, 'purchase'])->name('packages.purchase');
    Route::post('/packages/purchase', [PackageController::class, 'purchaseStore'])->name('packages.purchase.store');
    Route::get('/packages/{package}', [PackageController::class, 'view'])->name('packages.view');
    Route::get('/packages/{package}/edit', [PackageController::class, 'edit'])->name('packages.edit');
    Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');
    Route::post('/packages/{package}/toggle-status', [PackageController::class, 'toggleStatus'])->name('packages.toggle-status');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::view('/payments', 'backend.pages.coming-soon', ['module' => 'Payments'])->name('payments.index');
    Route::view('/payouts', 'backend.pages.coming-soon', ['module' => 'Payouts'])->name('payouts.index');
    Route::view('/wishlists', 'backend.pages.coming-soon', ['module' => 'Wishlists'])->name('wishlists.index');

    // Support Tickets
    Route::get('/support-tickets', [SupportTicketController::class, 'index'])->name('support-tickets.index');
    Route::get('/support-tickets/{ticket}', [SupportTicketController::class, 'show'])->name('support-tickets.show');
    Route::put('/support-tickets/{ticket}', [SupportTicketController::class, 'update'])->name('support-tickets.update');
    Route::delete('/support-tickets/{ticket}', [SupportTicketController::class, 'destroy'])->name('support-tickets.destroy');
    Route::post('/support-tickets/bulk-update', [SupportTicketController::class, 'bulkUpdate'])->name('support-tickets.bulk-update');

    Route::view('/conversations', 'backend.pages.coming-soon', ['module' => 'Conversations'])->name('conversations.index');
    Route::view('/notifications', 'backend.pages.coming-soon', ['module' => 'Notifications'])->name('notifications.index');

    // Content Library
    Route::get('/content-library', [ContentLibraryController::class, 'index'])->name('content-library');

    //Moderator routes (dashboard)
    Route::get('/moderators', [ModeratorController::class, 'index'])->name('moderators.index');
    Route::get('/moderators/create', [ModeratorController::class, 'create'])->name('moderators.create');
    Route::post('/moderators', [ModeratorController::class, 'store'])->name('moderators.store');
    Route::get('/moderators/{moderator}', [ModeratorController::class, 'show'])->name('moderators.show');
    Route::get('/moderators/{moderator}/edit', [ModeratorController::class, 'edit'])->name('moderators.edit');
    Route::put('/moderators/{moderator}', [ModeratorController::class, 'update'])->name('moderators.update');
    Route::delete('/moderators/{moderator}', [ModeratorController::class, 'destroy'])->name('moderators.destroy');
    Route::post('/moderators/{moderator}/toggle-status', [ModeratorController::class, 'toggleStatus'])->name('moderators.toggle-status');

    //Role routes (dashboard)
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('/roles/{id}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/{id}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');
    Route::get('/roles/{id}/permissions', [RoleController::class, 'getPermissions'])->name('roles.permissions');

    //Permission routes (dashboard)
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
    Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create');
    Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store');
    Route::get('/permissions/assign', [PermissionController::class, 'assign'])->name('permissions.assign');
    Route::post('/permissions/assign', [PermissionController::class, 'assignStore'])->name('permissions.assign.store');
    Route::get('/permissions/{id}', [PermissionController::class, 'show'])->name('permissions.show');
    Route::get('/permissions/{id}/edit', [PermissionController::class, 'edit'])->name('permissions.edit');
    Route::put('/permissions/{id}', [PermissionController::class, 'update'])->name('permissions.update');
    Route::delete('/permissions/{id}', [PermissionController::class, 'destroy'])->name('permissions.destroy');
    Route::post('/permissions/{id}/toggle-status', [PermissionController::class, 'toggleStatus'])->name('permissions.toggle-status');

    // Users routes (dashboard)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Case Studies routes
    Route::get('/case-studies', [CaseStudyController::class, 'index'])->name('case-studies.index');
    Route::get('/case-studies/create', [CaseStudyController::class, 'create'])->name('case-studies.create');
    Route::post('/case-studies', [CaseStudyController::class, 'store'])->name('case-studies.store');
    Route::get('/case-studies/{caseStudy}/edit', [CaseStudyController::class, 'edit'])->name('case-studies.edit');
    Route::put('/case-studies/{caseStudy}', [CaseStudyController::class, 'update'])->name('case-studies.update');
    Route::delete('/case-studies/{caseStudy}', [CaseStudyController::class, 'destroy'])->name('case-studies.destroy');
    Route::post('/case-studies/{caseStudy}/toggle-status', [CaseStudyController::class, 'toggleStatus'])->name('case-studies.toggle-status');

    // Testimonials routes
    Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
    Route::get('/testimonials/create', [TestimonialController::class, 'create'])->name('testimonials.create');
    Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');
    Route::get('/testimonials/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('testimonials.edit');
    Route::put('/testimonials/{testimonial}', [TestimonialController::class, 'update'])->name('testimonials.update');
    Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');
    Route::post('/testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status');

    // Featured Collaborations routes
    Route::get('/featured-collaborations', [FeaturedCollaborationController::class, 'index'])->name('featured-collaborations.index');
    Route::get('/featured-collaborations/create', [FeaturedCollaborationController::class, 'create'])->name('featured-collaborations.create');
    Route::post('/featured-collaborations', [FeaturedCollaborationController::class, 'store'])->name('featured-collaborations.store');
    Route::get('/featured-collaborations/{featuredCollaboration}/edit', [FeaturedCollaborationController::class, 'edit'])->name('featured-collaborations.edit');
    Route::patch('/featured-collaborations/{featuredCollaboration}', [FeaturedCollaborationController::class, 'update'])->name('featured-collaborations.update');
    Route::delete('/featured-collaborations/{featuredCollaboration}', [FeaturedCollaborationController::class, 'destroy'])->name('featured-collaborations.destroy');
    Route::patch('/featured-collaborations/{featuredCollaboration}/toggle-publish', [FeaturedCollaborationController::class, 'togglePublish'])->name('featured-collaborations.toggle-publish');

    // FAQ routes
    Route::get('/faqs/sections', [FaqController::class, 'indexSections'])->name('faqs.sections.index');
    Route::get('/faqs/sections/create', [FaqController::class, 'createSection'])->name('faqs.sections.create');
    Route::post('/faqs/sections', [FaqController::class, 'storeSection'])->name('faqs.sections.store');
    Route::get('/faqs/sections/{section}/edit', [FaqController::class, 'editSection'])->name('faqs.sections.edit');
    Route::put('/faqs/sections/{section}', [FaqController::class, 'updateSection'])->name('faqs.sections.update');
    Route::delete('/faqs/sections/{section}', [FaqController::class, 'destroySection'])->name('faqs.sections.destroy');
    Route::post('/faqs/sections/{section}/toggle-status', [FaqController::class, 'toggleSectionStatus'])->name('faqs.sections.toggle-status');

    Route::get('/faqs/sections/{section}/items', [FaqController::class, 'indexItems'])->name('faqs.items.index');
    Route::get('/faqs/sections/{section}/items/create', [FaqController::class, 'createItem'])->name('faqs.items.create');
    Route::post('/faqs/sections/{section}/items', [FaqController::class, 'storeItem'])->name('faqs.items.store');
    Route::get('/faqs/sections/{section}/items/{item}/edit', [FaqController::class, 'editItem'])->name('faqs.items.edit');
    Route::put('/faqs/sections/{section}/items/{item}', [FaqController::class, 'updateItem'])->name('faqs.items.update');
    Route::delete('/faqs/sections/{section}/items/{item}', [FaqController::class, 'destroyItem'])->name('faqs.items.destroy');
    Route::post('/faqs/sections/{section}/items/{item}/toggle-status', [FaqController::class, 'toggleItemStatus'])->name('faqs.items.toggle-status');

    // Knowledge Base routes
    Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledge-base.index');
    Route::get('/knowledge-base/create', [KnowledgeBaseController::class, 'create'])->name('knowledge-base.create');
    Route::post('/knowledge-base', [KnowledgeBaseController::class, 'store'])->name('knowledge-base.store');
    Route::get('/knowledge-base/{article}/edit', [KnowledgeBaseController::class, 'edit'])->name('knowledge-base.edit');
    Route::put('/knowledge-base/{article}', [KnowledgeBaseController::class, 'update'])->name('knowledge-base.update');
    Route::delete('/knowledge-base/{article}', [KnowledgeBaseController::class, 'destroy'])->name('knowledge-base.destroy');
    Route::post('/knowledge-base/{article}/toggle-status', [KnowledgeBaseController::class, 'toggleStatus'])->name('knowledge-base.toggle-status');

    // Brand Profile routes (dashboard)
    Route::get('/brand-profile/{slug}/edit', [BrandProfileController::class, 'edit'])->name('brand.profile.edit');
    Route::post('/brand-profile/{slug}/update', [BrandProfileController::class, 'update'])->name('brand.profile.update');
    Route::delete('/brand-profile/{slug}/profile-image', [BrandProfileController::class, 'deleteProfileImage'])->name('brand.profile.delete-image');
    Route::delete('/brand-profile/{slug}/cover-image', [BrandProfileController::class, 'deleteCoverImage'])->name('brand.profile.delete-cover');
    Route::post('/brand-profile/{slug}/toggle-verification', [BrandProfileController::class, 'toggleVerification'])->name('brand.profile.toggle-verification');
    Route::post('/brand-profile/{slug}/toggle-status', [BrandProfileController::class, 'toggleStatus'])->name('brand.profile.toggle-status');

    // Creator Profile routes (dashboard)
    Route::get('/creator-profile/{slug}/edit', [\App\Http\Controllers\CreatorProfileController::class, 'edit'])->name('creator.profile.edit');
    Route::post('/creator-profile/{slug}/update', [\App\Http\Controllers\CreatorProfileController::class, 'update'])->name('creator.profile.update');
    Route::delete('/creator-profile/{slug}/profile-image', [\App\Http\Controllers\CreatorProfileController::class, 'deleteProfileImage'])->name('creator.profile.delete-image');
    Route::delete('/creator-profile/{slug}/cover-image', [\App\Http\Controllers\CreatorProfileController::class, 'deleteCoverImage'])->name('creator.profile.delete-cover');
    Route::delete('/creator-profile/{slug}/portfolio/{portfolio}', [\App\Http\Controllers\CreatorProfileController::class, 'deletePortfolioImage'])->name('creator.portfolio.delete');
    Route::post('/creator-profile/{slug}/portfolio/{portfolio}/delete', [\App\Http\Controllers\CreatorProfileController::class, 'deletePortfolioImage'])->name('creator.portfolio.delete.post');
    Route::post('/creator-profile/{slug}/toggle-status', [\App\Http\Controllers\CreatorProfileController::class, 'toggleStatus'])->name('creator.profile.toggle-status');

    // Account routes
    Route::get('/account/{slug}', [AccountController::class, 'edit'])->name('account.edit');
    Route::post('/account/{slug}/details', [AccountController::class, 'updateDetails'])->name('account.details.update');
    Route::post('/account/{slug}/billing', [AccountController::class, 'updateBilling'])->name('account.billing.update');
    Route::post('/account/{slug}/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/account/{slug}/toggle-status', [AccountController::class, 'toggleStatus'])->name('account.toggle-status');
    Route::delete('/account/{slug}', [AccountController::class, 'destroy'])->name('account.destroy');

});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
 */

require __DIR__ . '/auth.php';
