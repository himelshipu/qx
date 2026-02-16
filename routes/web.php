<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;


Route::get('/', function () {
    return view('pages.general.home', ['title' => 'Homepage']);
})->name('home');
Route::get('/loggedin', function () {
    return view('pages.general.signin', ['title' => 'Sign In']);
})->name('signin');

Route::get('/brand-signup', function () {
    return view('pages.general.signup', ['title' => 'Brand Sign Up']);
})->name('brandsignup');


// dashboard pages
Route::get('/dashboard', function () {
    return view('pages.admin.dashboard.ecommerce', ['title' => 'E-commerce Dashboard']);
})->name('dashboard');

// calender pages
Route::get('/calendar', function () {
    return view('pages.admin.calender', ['title' => 'Calendar']);
})->name('calendar');

// profile pages
Route::get('/profile', function () {
    return view('pages.admin.profile', ['title' => 'Profile']);
})->name('profile');

// categories pages
Route::get('/categories', function () {
    return view('pages.admin.categories.categories', ['title' => 'Categories']);
})->name('categories');

// brand pages
Route::get('/create-brand', function () {
    return view('pages.admin.brand.create-brand', ['title' => 'Create Brand']);
})->name('create-brand');

// influencer pages
Route::get('/create-influencer', function () {
    return view('pages.admin.influencer.create-influencer', ['title' => 'Create Influencer']);
})->name('create-influencer');

// form pages
Route::get('/form-elements', function () {
    return view('pages.admin.form.form-elements', ['title' => 'Form Elements']);
})->name('form-elements');

// tables pages
Route::get('/basic-tables', function () {
    return view('pages.admin.tables.basic-tables', ['title' => 'Basic Tables']);
})->name('basic-tables');

// pages

Route::get('/blank', function () {
    return view('pages.admin.blank', ['title' => 'Blank']);
})->name('blank');

// error pages
Route::get('/error-404', function () {
    return view('pages.admin.errors.error-404', ['title' => 'Error 404']);
})->name('error-404');

// chart pages
Route::get('/line-chart', function () {
    return view('pages.admin.chart.line-chart', ['title' => 'Line Chart']);
})->name('line-chart');

Route::get('/bar-chart', function () {
    return view('pages.admin.chart.bar-chart', ['title' => 'Bar Chart']);
})->name('bar-chart');


// authentication pages
Route::get('/signin', function () {
    return view('pages.admin.auth.signin', ['title' => 'Sign In']);
})->name('signin');

Route::get('/signup', function () {
    return view('pages.admin.auth.signup', ['title' => 'Sign Up']);
})->name('signup');

// ui elements pages
Route::get('/alerts', function () {
    return view('pages.admin.ui-elements.alerts', ['title' => 'Alerts']);
})->name('alerts');

Route::get('/avatars', function () {
    return view('pages.admin.ui-elements.avatars', ['title' => 'Avatars']);
})->name('avatars');

Route::get('/badge', function () {
    return view('pages.admin.ui-elements.badges', ['title' => 'Badges']);
})->name('badges');

Route::get('/buttons', function () {
    return view('pages.admin.ui-elements.buttons', ['title' => 'Buttons']);
})->name('buttons');

Route::get('/image', function () {
    return view('pages.admin.ui-elements.images', ['title' => 'Images']);
})->name('images');

Route::get('/videos', function () {
    return view('pages.admin.ui-elements.videos', ['title' => 'Videos']);
})->name('videos');






















