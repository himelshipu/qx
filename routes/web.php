<?php

declare (strict_types = 1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\CampaignController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\CreatorController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ModeratorController;
use App\Http\Controllers\Backend\PermissionController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\BrandProfileController;
use App\Http\Controllers\Frontend\ContentLibraryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\StaticPagesController;
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
    Route::get('/support', [StaticPagesController::class, 'support'])->name('support');

    // Public profile pages
    Route::get('/creator/{id}', [\App\Http\Controllers\CreatorProfileController::class, 'show'])->name('creator.profile');
    Route::get('/brand/{id}', [\App\Http\Controllers\BrandProfileController::class, 'show'])->name('brand.profile');

    Route::get('/influencers', [StaticPagesController::class, 'influencers'])->name('influencers');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Breeze)
|--------------------------------------------------------------------------
 */
Route::prefix('dashboard')->name('dashboard.')->middleware(['auth', 'verified'])->group(function () {
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

    // Pending commerce and operations modules (placeholder pages)
    Route::view('/reviews', 'backend.pages.coming-soon', ['module' => 'Reviews'])->name('reviews.index');
    Route::view('/packages', 'backend.pages.coming-soon', ['module' => 'Packages'])->name('packages.index');
    Route::view('/orders', 'backend.pages.coming-soon', ['module' => 'Orders'])->name('orders.index');
    Route::view('/payments', 'backend.pages.coming-soon', ['module' => 'Payments'])->name('payments.index');
    Route::view('/payouts', 'backend.pages.coming-soon', ['module' => 'Payouts'])->name('payouts.index');
    Route::view('/wishlists', 'backend.pages.coming-soon', ['module' => 'Wishlists'])->name('wishlists.index');
    Route::view('/support-tickets', 'backend.pages.coming-soon', ['module' => 'Support Tickets'])->name('support-tickets.index');
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

    // Brand Profile routes (dashboard)
    Route::get('/brand-profile/edit', [BrandProfileController::class, 'edit'])->name('brand.profile.edit');
    Route::post('/brand-profile/update', [BrandProfileController::class, 'update'])->name('brand.profile.update');
    Route::delete('/brand-profile/profile-image', [BrandProfileController::class, 'deleteProfileImage'])->name('brand.profile.delete-image');
    Route::delete('/brand-profile/cover-image', [BrandProfileController::class, 'deleteCoverImage'])->name('brand.profile.delete-cover');
    Route::post('/brand-profile/toggle-verification', [BrandProfileController::class, 'toggleVerification'])->name('brand.profile.toggle-verification');
    Route::post('/brand-profile/toggle-status', [BrandProfileController::class, 'toggleStatus'])->name('brand.profile.toggle-status');

    // Creator Profile routes (dashboard)
    Route::get('/creator-profile/edit', [\App\Http\Controllers\CreatorProfileController::class, 'edit'])->name('creator.profile.edit');
    Route::post('/creator-profile/update', [\App\Http\Controllers\CreatorProfileController::class, 'update'])->name('creator.profile.update');
    Route::delete('/creator-profile/profile-image', [\App\Http\Controllers\CreatorProfileController::class, 'deleteProfileImage'])->name('creator.profile.delete-image');
    Route::delete('/creator-profile/cover-image', [\App\Http\Controllers\CreatorProfileController::class, 'deleteCoverImage'])->name('creator.profile.delete-cover');
    Route::post('/creator-profile/toggle-status', [\App\Http\Controllers\CreatorProfileController::class, 'toggleStatus'])->name('creator.profile.toggle-status');

    // Account routes
    Route::get('/account', [AccountController::class, 'edit'])->name('account.edit');
    Route::post('/account/details', [AccountController::class, 'updateDetails'])->name('account.details.update');
    Route::post('/account/billing', [AccountController::class, 'updateBilling'])->name('account.billing.update');
    Route::post('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::post('/account/toggle-status', [AccountController::class, 'toggleStatus'])->name('account.toggle-status');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');

});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
 */

require __DIR__ . '/auth.php';
