<?php

declare(strict_types=1);

use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\CampaignController;
use App\Http\Controllers\Backend\CreatorController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\StaticPagesController;
use App\Http\Controllers\ProfileController;
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

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/


require __DIR__.'/auth.php';
