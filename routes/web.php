<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\HomeController;
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
    
    // Custom login view - using existing custom design
    Route::get('/signin', function () {
        return view('pages.general.signin');
    })->name('login.view');
    
    // Also handle /login for Breeze password reset flow (GET only)
    // Named as 'login' so password.reset and other Breeze features work
    Route::get('/login', function () {
        return view('pages.general.signin');
    })->name('login');
    
    // Custom sign-up page with type query parameter
    Route::get('/sign-up', function (\Illuminate\Http\Request $request) {
        $type = $request->query('type', 'brand');
        
        // Validate type - fallback to brand if invalid
        if (!in_array($type, ['brand', 'creator'])) {
            $type = 'brand';
        }
        
        return view('pages.general.signup', ['type' => $type]);
    })->name('register');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Breeze)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

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
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['web', 'auth', 'verified'])
    ->group(function () {
        // Dashboard
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // Additional admin routes can be added here
    });

require __DIR__.'/auth.php';
