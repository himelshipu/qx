<?php

declare (strict_types = 1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\CampaignController;
use App\Http\Controllers\Backend\CampaignInfluencerController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\CaseStudyController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\CategoryFeaturedController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\FeaturedCollaborationController;
use App\Http\Controllers\Backend\InfluencerController;
use App\Http\Controllers\Backend\InfluencerPortfolioController;
use App\Http\Controllers\Backend\KnowledgeBaseController;
use App\Http\Controllers\Backend\MenuController;
use App\Http\Controllers\Backend\ModeratorController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\OrderController as BackendOrderController;
use App\Http\Controllers\Backend\PackageController;
use App\Http\Controllers\Backend\PaymentAuditController;
use App\Http\Controllers\Backend\PaymentQueueController;
use App\Http\Controllers\Backend\PaymentsController;
use App\Http\Controllers\Backend\PaymentStatementController;
use App\Http\Controllers\Backend\PayoutsController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\ReviewController as BackendReviewController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SupportTicketController;
use App\Http\Controllers\Backend\StaticPageController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\BrandProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\EarningsController;
use App\Http\Controllers\Frontend\CampaignController as FrontendCampaignController;
use App\Http\Controllers\Frontend\BlogController as FrontendBlogController;
use App\Http\Controllers\Frontend\CaseStudyController as FrontendCaseStudyController;
use App\Http\Controllers\Frontend\ContentLibraryController;
use App\Http\Controllers\Frontend\AccountController as FrontendAccountController;
use App\Http\Controllers\Frontend\ConversationController as FrontendConversationController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\InfluencersController;
use App\Http\Controllers\Frontend\KnowledgeBaseController as FrontendKnowledgeBaseController;
use App\Http\Controllers\Frontend\NotificationController as FrontendNotificationController;
use App\Http\Controllers\Frontend\OrderController as FrontendOrderController;
use App\Http\Controllers\Frontend\PackageController as FrontendPackageController;
use App\Http\Controllers\Frontend\PaymentAuditController as FrontendPaymentAuditController;
use App\Http\Controllers\Frontend\PaymentQueueController as FrontendPaymentQueueController;
use App\Http\Controllers\Frontend\PaymentStatementController as FrontendPaymentStatementController;
use App\Http\Controllers\Frontend\StaticPagesController;
use App\Http\Controllers\Frontend\SupportTicketController as FrontendSupportTicketController;
use App\Http\Controllers\InfluencerProfileController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PublicPageController;
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
    Route::get('/influencer/{slug}', [InfluencerProfileController::class, 'show'])->name('influencer.profile');
    Route::get('/brand/{slug}', [BrandProfileController::class, 'show'])->name('brand.profile');

    // Authenticated conversation negotiation
    Route::get('/influencer/{influencer}/start-negotiation', [ConversationController::class, 'startNegotiation'])->name('conversations.start-negotiation');

    // Package cart operations
    Route::get('/package/{package}/add-to-cart', [CartController::class, 'startAddToCart'])->name('cart.start-add-to-cart');

    // Influencers pages
    Route::get('/influencers', [InfluencersController::class, 'index'])->name('influencers');
    Route::get('/influencers/{platformSlug}', [InfluencersController::class, 'index'])->name('influencers.platform');
    Route::get('/category/{categorySlug}', [InfluencersController::class, 'byCategory'])->name('influencers.category');
    Route::get('/ugc', [InfluencersController::class, 'ugc'])->name('influencers.ugc');

    // Static pages (privacy, terms, about, contact, etc.)
    Route::get('/page/{slug}', [PublicPageController::class, 'show'])->name('pages.show');

    // Case Studies page
    Route::get('/case-studies', [FrontendCaseStudyController::class, 'index'])->name('case-studies');
    Route::get('/case-studies/{caseStudy:slug}', [FrontendCaseStudyController::class, 'show'])->name('case-studies.show');

    // Public Blog
    Route::get('/blogs', [FrontendBlogController::class, 'index'])->name('frontend.blogs.index');
    Route::get('/blogs/{blogPost:slug}', [FrontendBlogController::class, 'show'])->name('frontend.blogs.show');

    // API endpoints
    Route::get('/api/categories', [InfluencersController::class, 'apiCategories']);
});

/*
|--------------------------------------------------------------------------
| Frontend Authenticated Routes (Brand & Influencer Users)
|--------------------------------------------------------------------------
 */
Route::middleware(['auth', 'verified'])->group(function () {
    // Frontend Campaigns (Brand)
    Route::get('/campaigns', [FrontendCampaignController::class, 'index'])->name('frontend.campaigns.index');
    Route::get('/campaigns/create', [FrontendCampaignController::class, 'create'])->name('frontend.campaigns.create');
    Route::post('/campaigns', [FrontendCampaignController::class, 'store'])->name('frontend.campaigns.store');
    Route::get('/campaigns/{campaign}', [FrontendCampaignController::class, 'show'])->name('frontend.campaigns.show');
    Route::post('/campaigns/{campaign}/apply', [FrontendCampaignController::class, 'apply'])->name('frontend.campaigns.apply');
    Route::get('/campaigns/{campaign}/edit', [FrontendCampaignController::class, 'edit'])->name('frontend.campaigns.edit');
    Route::put('/campaigns/{campaign}', [FrontendCampaignController::class, 'update'])->name('frontend.campaigns.update');
    Route::delete('/campaigns/{campaign}', [FrontendCampaignController::class, 'destroy'])->name('frontend.campaigns.destroy');
    Route::patch('/campaigns/{campaign}/status', [FrontendCampaignController::class, 'updateStatus'])->name('frontend.campaigns.update-status');
    Route::post('/campaigns/{campaign}/applications/{application}/update-status', [FrontendCampaignController::class, 'updateApplicationStatus'])->name('frontend.campaigns.update-application-status');
    Route::post('/campaigns/{campaign}/applications/{application}/brand-update-work-status', [FrontendCampaignController::class, 'updateBrandWorkStatus'])->name('frontend.campaigns.brand-update-work-status');
    Route::post('/campaigns/applications/{application}/withdraw', [FrontendCampaignController::class, 'withdrawApplication'])->name('frontend.campaigns.withdraw-application');
    Route::post('/campaigns/applications/{application}/update-work-status', [FrontendCampaignController::class, 'updateInfluencerWorkStatus'])->name('frontend.campaigns.update-work-status');
    Route::post('/campaigns/{campaign}/influencer-assignments/{assignment}/update-status', [FrontendCampaignController::class, 'updateInfluencerStatus'])->name('campaigns.update-influencer-status');

    // Frontend Packages (Influencer)
    Route::get('/packages', [FrontendPackageController::class, 'index'])->name('frontend.packages.index');
    Route::get('/packages/create', [FrontendPackageController::class, 'create'])->name('frontend.packages.create');
    Route::post('/packages', [FrontendPackageController::class, 'store'])->name('frontend.packages.store');
    Route::get('/packages/{package}', [FrontendPackageController::class, 'show'])->name('frontend.packages.show');
    Route::get('/packages/{package}/edit', [FrontendPackageController::class, 'edit'])->name('frontend.packages.edit');
    Route::put('/packages/{package}', [FrontendPackageController::class, 'update'])->name('frontend.packages.update');
    Route::delete('/packages/{package}', [FrontendPackageController::class, 'destroy'])->name('frontend.packages.destroy');

    // Frontend Cart
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
    Route::post('/cart/complete-checkout', [CartController::class, 'completeCheckout'])->name('cart.complete-checkout');
    Route::delete('/cart/items/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::put('/cart/items/{cartItem}', [CartController::class, 'updateQuantity'])->name('cart.update');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Frontend Orders
    Route::get('/orders', [FrontendOrderController::class, 'index'])->name('frontend.orders.index');
    Route::get('/orders/{order}', [FrontendOrderController::class, 'show'])->name('frontend.orders.show');
    Route::put('/orders/{order}/items/{item}/status', [FrontendOrderController::class, 'updateItemStatus'])->name('frontend.orders.items.update-status');
    Route::put('/orders/{order}/items/{item}/decision', [FrontendOrderController::class, 'updateBrandItemDecision'])->name('frontend.orders.items.update-decision');
    Route::post('/orders/{order}/items/{item}/review', [FrontendOrderController::class, 'storeBrandTaskReview'])->name('frontend.orders.items.reviews.store');
    Route::put('/orders/{order}/complete', [FrontendOrderController::class, 'completeOrder'])->name('frontend.orders.complete');
    Route::post('/orders/{order}/reviews', [FrontendOrderController::class, 'storeReview'])->name('frontend.orders.reviews.store');

    // Frontend Conversations
    Route::get('/messages', [FrontendConversationController::class, 'index'])->name('frontend.conversations.index');
    Route::get('/messages/open/{influencer}/{order?}', [FrontendConversationController::class, 'openOrderConversation'])->name('frontend.conversations.open-order');
    Route::get('/messages/{conversation:public_id}', [FrontendConversationController::class, 'show'])->name('frontend.conversations.show');
    Route::post('/messages/{conversation:public_id}/send', [FrontendConversationController::class, 'storeMessage'])->name('frontend.conversations.storeMessage');

    // Frontend Content Library
    Route::get('/content-library', [ContentLibraryController::class, 'index'])->name('frontend.content-library');

    // Account Management (shared for both brand and Influencer)
    Route::get('/account/{slug}', [FrontendAccountController::class, 'edit'])->name('frontend.account.edit');
    Route::post('/account/{slug}/details', [FrontendAccountController::class, 'updateDetails'])->name('frontend.account.details.update');
    Route::post('/account/{slug}/billing', [FrontendAccountController::class, 'updateBilling'])->name('frontend.account.billing.update');
    Route::post('/account/{slug}/password', [FrontendAccountController::class, 'updatePassword'])->name('frontend.account.password.update');
    Route::post('/account/{slug}/toggle-status', [FrontendAccountController::class, 'toggleStatus'])->name('frontend.account.toggle-status');

    // Brand Profile routes
    Route::get('/brand-profile/{slug}/edit', [BrandProfileController::class, 'edit'])->name('brand.profile.edit');
    Route::post('/brand-profile/{slug}/update', [BrandProfileController::class, 'update'])->name('brand.profile.update');
    Route::delete('/brand-profile/{slug}/profile-image', [BrandProfileController::class, 'deleteProfileImage'])->name('brand.profile.delete-image');
    Route::delete('/brand-profile/{slug}/cover-image', [BrandProfileController::class, 'deleteCoverImage'])->name('brand.profile.delete-cover');
    Route::post('/brand-profile/{slug}/toggle-verification', [BrandProfileController::class, 'toggleVerification'])->name('brand.profile.toggle-verification');
    Route::post('/brand-profile/{slug}/toggle-status', [BrandProfileController::class, 'toggleStatus'])->name('brand.profile.toggle-status');

    // Influencer Profile routes
    Route::get('/influencer-profile/{slug}/edit', [InfluencerProfileController::class, 'edit'])->name('influencer.profile.edit');
    Route::post('/influencer-profile/{slug}/update', [InfluencerProfileController::class, 'update'])->name('influencer.profile.update');
    Route::delete('/influencer-profile/{slug}/profile-image', [InfluencerProfileController::class, 'deleteProfileImage'])->name('influencer.profile.delete-image');
    Route::delete('/influencer-profile/{slug}/cover-image', [InfluencerProfileController::class, 'deleteCoverImage'])->name('influencer.profile.delete-cover');
    Route::delete('/influencer-profile/{slug}/portfolio/{portfolio}', [InfluencerProfileController::class, 'deletePortfolioImage'])->name('influencer.portfolio.delete');
    Route::post('/influencer-profile/{slug}/portfolio/{portfolio}/delete', [InfluencerProfileController::class, 'deletePortfolioImage'])->name('influencer.portfolio.delete.post');
    Route::post('/influencer-profile/{slug}/toggle-status', [InfluencerProfileController::class, 'toggleStatus'])->name('influencer.profile.toggle-status');

    // Payment Method routes (for authenticated users)
    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods.index');
    Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('payment-methods.store');
    Route::post('/payment-methods/{paymentMethod}/set-default', [PaymentMethodController::class, 'setDefault'])->name('payment-methods.set-default');
    Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('payment-methods.destroy');

    // AJAX API routes
    Route::get('/payment-methods/api/list', [PaymentMethodController::class, 'getJson'])->name('payment-methods.api.list');
    Route::get('/payment-methods/api/default', [PaymentMethodController::class, 'getDefaultJson'])->name('payment-methods.api.default');

    // Earnings routes (influencers only)
    Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings.index');

    // Payment Queue routes (influencers only)
    Route::get('/payment-queue', [FrontendPaymentQueueController::class, 'index'])->name('payment-queue.index');

    // Payment Audit Log routes (influencers only)
    Route::get('/payment-audit', [FrontendPaymentAuditController::class, 'index'])->name('payment-audit.index');

    // Payment Statement routes (influencers only)
    Route::get('/payment-statements', [FrontendPaymentStatementController::class, 'index'])->name('payment-statements.index');
    Route::get('/payment-statements/pdf', [FrontendPaymentStatementController::class, 'pdf'])->name('payment-statements.pdf');
    Route::get('/payment-statements/pdf/monthly', [FrontendPaymentStatementController::class, 'monthlyPdf'])->name('payment-statements.pdf.monthly');

    // Notification routes (both brand and influencer users)
    Route::prefix('notifications')->name('frontend.notifications.')->group(function () {
        Route::get('/', [FrontendNotificationController::class, 'index'])->name('index');
        Route::get('/api/unread', [FrontendNotificationController::class, 'getUnread'])->name('api.unread');
        Route::post('/{notification}/mark-as-read', [FrontendNotificationController::class, 'markAsRead'])->name('mark-as-read');
        Route::post('/{notification}/mark-as-unread', [FrontendNotificationController::class, 'markAsUnread'])->name('mark-as-unread');
        Route::post('/mark-all-as-read', [FrontendNotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        Route::delete('/{notification}', [FrontendNotificationController::class, 'destroy'])->name('destroy');
        Route::post('/clear-all', [FrontendNotificationController::class, 'clearAll'])->name('clear-all');
        Route::get('/{notification}/show', [FrontendNotificationController::class, 'show'])->name('show');
    });
});

/*
|--------------------------------------------------------------------------
| Admin/Moderator Dashboard Routes
|--------------------------------------------------------------------------
 */
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'verified', 'restrict-dashboard-access', 'dashboard-route-permission'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('index');
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/table', [CategoryController::class, 'table'])->name('categories.table');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    Route::post('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');

    // Featured Categories Routes
    Route::prefix('categories-featured')->name('categories-featured.')->group(function () {
        Route::get('/', [CategoryFeaturedController::class, 'getFeatured'])->name('list');
        Route::post('/add/{category}', [CategoryFeaturedController::class, 'addFeatured'])->name('add');
        Route::post('/remove/{category}', [CategoryFeaturedController::class, 'removeFeatured'])->name('remove');
        Route::post('/reorder', [CategoryFeaturedController::class, 'reorderFeatured'])->name('reorder');
        Route::get('/search', [CategoryFeaturedController::class, 'searchCategories'])->name('search');
    });

    Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
    Route::get('/brands/create', [BrandController::class, 'create'])->name('brands.create');
    Route::post('/brands', [BrandController::class, 'store'])->name('brands.store');
    Route::get('/brands/details/{brand}', [BrandController::class, 'view'])->name('brands.view');
    Route::get('/brands/{brand}/edit', [BrandController::class, 'edit'])->name('brands.edit');
    Route::put('/brands/{brand}', [BrandController::class, 'update'])->name('brands.update');
    Route::delete('/brands/{brand}', [BrandController::class, 'destroy'])->name('brands.destroy');
    Route::post('/brands/{brand}/toggle-status', [BrandController::class, 'toggleStatus'])->name('brands.toggle-status');
    Route::post('/brands/reorder', [BrandController::class, 'reorder'])
        ->middleware('check-permission:brands.reorder')
        ->name('brands.reorder');

    Route::get('/influencers', [InfluencerController::class, 'index'])->name('influencers.index');
    Route::get('/influencers/create', [InfluencerController::class, 'create'])->name('influencers.create');
    Route::post('/influencers', [InfluencerController::class, 'store'])->name('influencers.store');
    Route::get('/influencers/details/{influencer}', [InfluencerController::class, 'view'])->name('influencers.view');
    Route::get('/influencers/{influencer}/edit', [InfluencerController::class, 'edit'])->name('influencers.edit');
    Route::put('/influencers/{influencer}', [InfluencerController::class, 'update'])->name('influencers.update');
    Route::delete('/influencers/{influencer}', [InfluencerController::class, 'destroy'])->name('influencers.destroy');
    Route::post('/influencers/{influencer}/toggle-status', [InfluencerController::class, 'toggleStatus'])->name('influencers.toggle-status');
    Route::post('/influencers/{influencer}/toggle-featured', [InfluencerController::class, 'toggleFeatured'])->name('influencers.toggle-featured');
    Route::post('/influencers/reorder', [InfluencerController::class, 'reorder'])
        ->middleware('check-permission:influencers.reorder')
        ->name('influencers.reorder');

    // Influencer Portfolio Management
    Route::get('/influencers/{influencer}/portfolio', [InfluencerPortfolioController::class, 'index'])->name('influencers.portfolio.index');
    Route::get('/influencers/{influencer}/portfolio/create', [InfluencerPortfolioController::class, 'create'])->name('influencers.portfolio.create');
    Route::post('/influencers/{influencer}/portfolio', [InfluencerPortfolioController::class, 'store'])->name('influencers.portfolio.store');
    Route::get('/influencers/{influencer}/portfolio/{portfolio}/edit', [InfluencerPortfolioController::class, 'edit'])->name('influencers.portfolio.edit');
    Route::put('/influencers/{influencer}/portfolio/{portfolio}', [InfluencerPortfolioController::class, 'update'])->name('influencers.portfolio.update');
    Route::delete('/influencers/{influencer}/portfolio/{portfolio}', [InfluencerPortfolioController::class, 'destroy'])->name('influencers.portfolio.destroy');
    Route::post('/influencers/{influencer}/portfolio/reorder', [InfluencerPortfolioController::class, 'reorder'])->name('influencers.portfolio.reorder');
    Route::post('/influencers/{influencer}/portfolio/{portfolio}/toggle', [InfluencerPortfolioController::class, 'toggle'])->name('influencers.portfolio.toggle');

    // Admin campaigns
    Route::get('/campaigns/standard', [CampaignController::class, 'index'])->name('campaigns.standard');
    Route::get('/campaigns/standard/create', [CampaignController::class, 'create'])->name('campaigns.standard.create');
    Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store');
    Route::get('/campaigns/details/{campaign}', [CampaignController::class, 'view'])->name('campaigns.view');
    Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit');
    Route::put('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update');
    Route::post('/campaigns/{campaign}/update-status', [CampaignController::class, 'updateStatus'])->name('campaigns.update-status');
    Route::post('/campaigns/{campaign}/applications/{application}/update-status', [FrontendCampaignController::class, 'updateApplicationStatus'])->name('campaigns.update-application-status');
    Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy');
    Route::get('/campaigns/assign', [CampaignController::class, 'assign'])->name('campaigns.assign');
    Route::post('/campaigns/assign', [CampaignController::class, 'assignStore'])->name('campaigns.assign.store');
    Route::get('/campaigns/{campaign}/assigned-influencers', [CampaignController::class, 'assignedInfluencersJson'])->name('campaigns.assigned-influencers');

    // Campaign Influencer Management (Workflow A)
    Route::prefix('campaigns/{campaign}/influencers')->name('campaigns.influencers.')->group(function () {
        Route::get('/', [CampaignInfluencerController::class, 'index'])->name('index');
        Route::get('/create', [CampaignInfluencerController::class, 'create'])->name('create');
        Route::post('/', [CampaignInfluencerController::class, 'store'])->name('store');
    });
    Route::prefix('campaign-influencers')->name('campaign-influencers.')->group(function () {
        Route::post('{campaignInfluencer}/approve', [CampaignInfluencerController::class, 'approve'])->name('approve');
        Route::post('{campaignInfluencer}/reject', [CampaignInfluencerController::class, 'reject'])->name('reject');
        Route::post('{campaignInfluencer}/cancel', [CampaignInfluencerController::class, 'cancel'])->name('cancel');
        Route::delete('{campaignInfluencer}', [CampaignInfluencerController::class, 'destroy'])->name('destroy');
    });

    // Commerce and operations modules
    Route::get('/reviews', [BackendReviewController::class, 'index'])->name('reviews.index');
    Route::get('/reviews/{review}', [BackendReviewController::class, 'show'])->name('reviews.show');
    Route::post('/reviews/{review}/toggle-visibility', [BackendReviewController::class, 'toggleVisibility'])->name('reviews.toggle-visibility');
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

    Route::get('/orders', [BackendOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [BackendOrderController::class, 'show'])->name('orders.show');
    Route::put('/orders/{order}/status', [BackendOrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/create-from-campaign', [BackendOrderController::class, 'createFromCampaign'])->name('orders.create-from-campaign');
    Route::put('/sub-orders/{subOrder}/status', [BackendOrderController::class, 'updateSubOrderStatus'])->name('sub-orders.update-status');
    Route::post('/sub-orders/{subOrder}/mark-paid', [BackendOrderController::class, 'markSubOrderPaid'])->name('sub-orders.mark-paid');
    Route::put('/order-items/{orderItem}/status', [BackendOrderController::class, 'updateOrderItemStatus'])->name('order-items.update-status');
    Route::post('/order-items/{orderItem}/mark-paid', [BackendOrderController::class, 'markOrderItemPaid'])->name('order-items.mark-paid');

    // Payments & Payouts
    Route::get('/payments', [PaymentsController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [PaymentsController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/refund', [PaymentsController::class, 'refund'])->name('payments.refund');
    Route::post('/payments/{payment}/retry', [PaymentsController::class, 'retry'])->name('payments.retry');

    Route::get('/payouts', [PayoutsController::class, 'index'])->name('payouts.index');
    Route::post('/payouts', [PayoutsController::class, 'store'])->name('payouts.store');
    Route::get('/payouts/{payout}', [PayoutsController::class, 'show'])->name('payouts.show');
    Route::put('/payouts/{payout}', [PayoutsController::class, 'update'])->name('payouts.update');
    Route::post('/payouts/{payout}/mark-paid', [PayoutsController::class, 'markAsPaid'])->name('payouts.mark-paid');
    Route::get('/payouts/influencer/{influencer}/accounts', [PayoutsController::class, 'getInfluencerAccounts'])->name('payouts.influencer-accounts');

    // Payment Queue, Audit & Statements
    Route::get('/payment-queue', [PaymentQueueController::class, 'index'])->name('payment-queue.index');
    Route::post('/payment-queue/bulk-mark', [PaymentQueueController::class, 'bulkMark'])->name('payment-queue.bulk-mark');

    Route::get('/payment-audit', [PaymentAuditController::class, 'index'])->name('payment-audit.index');
    Route::post('/payment-audit/undo-item/{orderItem}', [PaymentAuditController::class, 'undo'])->name('payment-audit.undo-item');
    Route::post('/payment-audit/undo-suborder/{subOrder}', [PaymentAuditController::class, 'undoSubOrder'])->name('payment-audit.undo-suborder');

    Route::get('/payment-statement', [PaymentStatementController::class, 'index'])->name('payment-statement.index');
    Route::get('/payment-statement/{influencer}', [PaymentStatementController::class, 'show'])->name('payment-statement.show');
    Route::get('/payment-statement/{influencer}/pdf', [PaymentStatementController::class, 'pdf'])->name('payment-statement.pdf');

    // Conversations (Chat with Moderator Mediation)
    Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
    Route::get('/conversations/{conversation:public_id}', [ConversationController::class, 'show'])->name('conversations.show');
    Route::post('/conversations/{conversation:public_id}/messages', [ConversationController::class, 'storeMessage'])->name('conversations.storeMessage');
    Route::post('/conversations/{conversation:public_id}/assign-moderator', [ConversationController::class, 'assignModerator'])->name('conversations.assign-moderator');

    // Support Tickets
    Route::get('/support-tickets', [SupportTicketController::class, 'index'])->name('support-tickets.index');
    Route::get('/support-tickets/{ticket}', [SupportTicketController::class, 'show'])->name('support-tickets.show');
    Route::put('/support-tickets/{ticket}', [SupportTicketController::class, 'update'])->name('support-tickets.update');
    Route::delete('/support-tickets/{ticket}', [SupportTicketController::class, 'destroy'])->name('support-tickets.destroy');
    Route::post('/support-tickets/bulk-update', [SupportTicketController::class, 'bulkUpdate'])->name('support-tickets.bulk-update');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/api/unread', [NotificationController::class, 'getUnread'])->name('notifications.api.unread');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/{notification}/mark-as-unread', [NotificationController::class, 'markAsUnread'])->name('notifications.mark-as-unread');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::delete('/notifications/clear-all', [NotificationController::class, 'clearAll'])->name('notifications.clear-all');
    Route::get('/notifications/{notification}/show', [NotificationController::class, 'show'])->name('notifications.show');

    // Role routes (dashboard)
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/data', [RoleController::class, 'getRoleData'])->name('roles.get-data');
    Route::get('/roles/{id}/permissions/names', [RoleController::class, 'getPermissions'])->name('roles.permissions');
    Route::put('/roles/{id}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/roles/{id}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status');

    // Permission routes (for assigning permissions to roles)
    Route::get('/permissions/assign', [PermissionController::class, 'assign'])->name('permissions.assign');
    Route::post('/permissions/assign', [PermissionController::class, 'assignStore'])->name('permissions.assign.store');

    // Users routes (dashboard)
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    // Moderator routes (dashboard)
    Route::get('/moderators', [ModeratorController::class, 'index'])->name('moderators.index');
    Route::get('/moderators/create', [ModeratorController::class, 'create'])->name('moderators.create');
    Route::post('/moderators', [ModeratorController::class, 'store'])->name('moderators.store');
    Route::get('/moderators/{moderator}', [ModeratorController::class, 'show'])->name('moderators.show');
    Route::get('/moderators/{moderator}/edit', [ModeratorController::class, 'edit'])->name('moderators.edit');
    Route::put('/moderators/{moderator}', [ModeratorController::class, 'update'])->name('moderators.update');
    Route::delete('/moderators/{moderator}', [ModeratorController::class, 'destroy'])->name('moderators.destroy');
    Route::post('/moderators/{moderator}/toggle-status', [ModeratorController::class, 'toggleStatus'])->name('moderators.toggle-status');

    // User Roles routes (for assigning roles to users)
    Route::get('/users/roles/assign', [UserController::class, 'assignRoles'])->name('users.roles.assign');
    Route::post('/users/roles/assign', [UserController::class, 'assignRolesStore'])->name('users.roles.assign.store');
    Route::get('/users/{user}/roles', [UserController::class, 'getUserRoles'])->name('users.roles.get');

    // Case Studies routes
    Route::get('/case-studies', [CaseStudyController::class, 'index'])->name('case-studies.index');
    Route::get('/case-studies/create', [CaseStudyController::class, 'create'])->name('case-studies.create');
    Route::post('/case-studies', [CaseStudyController::class, 'store'])->name('case-studies.store');
    Route::get('/case-studies/{caseStudy}', [CaseStudyController::class, 'show'])->name('case-studies.show');
    Route::get('/case-studies/{caseStudy}/edit', [CaseStudyController::class, 'edit'])->name('case-studies.edit');
    Route::put('/case-studies/{caseStudy}', [CaseStudyController::class, 'update'])->name('case-studies.update');
    Route::delete('/case-studies/{caseStudy}', [CaseStudyController::class, 'destroy'])->name('case-studies.destroy');
    Route::post('/case-studies/{caseStudy}/toggle-status', [CaseStudyController::class, 'toggleStatus'])->name('case-studies.toggle-status');
    Route::post('/case-studies/reorder', [CaseStudyController::class, 'reorder'])
        ->middleware('check-permission:case-studies.reorder')
        ->name('case-studies.reorder');

    // Testimonials routes
    Route::get('/testimonials', [TestimonialController::class, 'index'])->name('testimonials.index');
    Route::get('/testimonials/create', [TestimonialController::class, 'create'])->name('testimonials.create');
    Route::post('/testimonials', [TestimonialController::class, 'store'])->name('testimonials.store');
    Route::get('/testimonials/{testimonial}/edit', [TestimonialController::class, 'edit'])->name('testimonials.edit');
    Route::put('/testimonials/{testimonial}', [TestimonialController::class, 'update'])->name('testimonials.update');
    Route::delete('/testimonials/{testimonial}', [TestimonialController::class, 'destroy'])->name('testimonials.destroy');
    Route::post('/testimonials/{testimonial}/toggle-status', [TestimonialController::class, 'toggleStatus'])->name('testimonials.toggle-status');
    Route::post('/testimonials/reorder', [TestimonialController::class, 'reorder'])
        ->middleware('check-permission:testimonials.reorder')
        ->name('testimonials.reorder');

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

    // Static Pages routes
    Route::get('/static-pages', [StaticPageController::class, 'index'])->name('static-pages.index');
    Route::get('/static-pages/create', [StaticPageController::class, 'create'])->name('static-pages.create');
    Route::post('/static-pages', [StaticPageController::class, 'store'])->name('static-pages.store');
    Route::get('/static-pages/{staticPage}', [StaticPageController::class, 'show'])->name('static-pages.show');
    Route::get('/static-pages/{staticPage}/edit', [StaticPageController::class, 'edit'])->name('static-pages.edit');
    Route::put('/static-pages/{staticPage}', [StaticPageController::class, 'update'])->name('static-pages.update');
    Route::delete('/static-pages/{staticPage}', [StaticPageController::class, 'destroy'])->name('static-pages.destroy');
    Route::post('/static-pages/{staticPage}/toggle-status', [StaticPageController::class, 'toggleStatus'])->name('static-pages.toggle-status');

    // Blog routes
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index');
    Route::get('/blogs/create', [BlogController::class, 'create'])->name('blogs.create');
    Route::post('/blogs', [BlogController::class, 'store'])->name('blogs.store');
    Route::get('/blogs/{blogPost}', [BlogController::class, 'show'])->name('blogs.show');
    Route::get('/blogs/{blogPost}/edit', [BlogController::class, 'edit'])->name('blogs.edit');
    Route::put('/blogs/{blogPost}', [BlogController::class, 'update'])->name('blogs.update');
    Route::delete('/blogs/{blogPost}', [BlogController::class, 'destroy'])->name('blogs.destroy');
    Route::post('/blogs/{blogPost}/toggle-status', [BlogController::class, 'toggleStatus'])->name('blogs.toggle-status');
    Route::post('/blogs/{slug}/restore', [BlogController::class, 'restore'])->name('blogs.restore');

    // Settings routes
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::post('/settings/update-order', [SettingsController::class, 'updateOrder'])->name('settings.update-order');
    Route::post('/settings/recovery/{type}/{id}/restore', [SettingsController::class, 'restoreEntity'])->name('settings.recovery.restore');

    // Profile routes (dashboard-only)
    Route::get('/profile', [AccountController::class, 'profileEdit'])->name('profile.edit');
    Route::post('/profile', [AccountController::class, 'profileUpdate'])->name('profile.update');

    // Account routes (dashboard-only)
    Route::get('/account/{slug}', [AccountController::class, 'edit'])->name('account.edit');
    Route::post('/account/{slug}/details', [AccountController::class, 'updateDetails'])->name('account.details.update');
    Route::post('/account/{slug}/billing', [AccountController::class, 'updateBilling'])->name('account.billing.update');
    Route::post('/account/{slug}/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/account/{slug}/toggle-status', [AccountController::class, 'toggleStatus'])->name('account.toggle-status');
    Route::delete('/account/{slug}', [AccountController::class, 'destroy'])->name('account.destroy');

    // Dashboard Menu & Permissions API routes
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/menu', [MenuController::class, 'getDashboardMenu'])->name('menu');
        Route::get('/permissions', [MenuController::class, 'getUserPermissions'])->name('permissions');
        Route::post('/check-permission', [MenuController::class, 'checkPermission'])->name('check-permission');
        Route::post('/check-action', [MenuController::class, 'checkAction'])->name('check-action');
    });
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
 */

require __DIR__ . '/auth.php';
