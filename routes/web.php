<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\CampaignController;
use App\Http\Controllers\Backend\CreatorController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\BrandProfileController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\StaticPagesController;
use App\Http\Controllers\Frontend\ContentLibraryController;
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
Route::prefix('dashboard') ->name('dashboard.')->middleware(['auth', 'verified'])->group(function () {
    // Dashboard
   
     Route::get('/', [DashboardController::class, 'index'])->name('index');
     Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
     Route::get('/brands', [BrandController::class, 'index'])->name('brands.index');
     Route::get('/brands/details/{id}', [BrandController::class, 'view'])->name('brands.view');
     Route::get('/creators', [CreatorController::class, 'index'])->name('creators.index');
     Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
     Route::get('/content-library', [ContentLibraryController::class, 'index'])->name('content-library');






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


require __DIR__.'/auth.php';
