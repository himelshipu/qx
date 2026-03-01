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

    //newly added routes for creator profile and edit profile Fahman
    Route::get('/creator-edit-profile', [StaticPagesController::class, 'creatorEditProfile'])->name('creator-edit-profile');
    Route::get('/creator-profile', [StaticPagesController::class, 'creatorProfile'])->name('creator-profile');
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






  // Brand Profile routes
    Route::get('/brand-profile/edit', [BrandProfileController::class, 'edit'])->name('brand.profile.edit');
    Route::post('/brand-profile/update', [BrandProfileController::class, 'update'])->name('brand.profile.update');
    Route::delete('/brand-profile/profile-image', [BrandProfileController::class, 'deleteProfileImage'])->name('brand.profile.delete-image');
    Route::delete('/brand-profile/cover-image', [BrandProfileController::class, 'deleteCoverImage'])->name('brand.profile.delete-cover');
    Route::post('/brand-profile/toggle-verification', [BrandProfileController::class, 'toggleVerification'])->name('brand.profile.toggle-verification');
    Route::post('/brand-profile/toggle-status', [BrandProfileController::class, 'toggleStatus'])->name('brand.profile.toggle-status');

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
