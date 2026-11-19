<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SeoController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

// Destinations
Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
Route::get('/destinations/country/{country}', [DestinationController::class, 'country'])
    ->whereIn('country', ['nepal', 'india', 'tibet'])->name('destinations.country');
Route::get('/destinations/{destination}', [DestinationController::class, 'show'])->name('destinations.show');

// Tour packages
Route::get('/packages', [PackageController::class, 'index'])->name('packages.index');
Route::get('/packages/type/{category}', [PackageController::class, 'category'])->name('packages.category');
Route::get('/packages/{package}', [PackageController::class, 'show'])->name('packages.show');

// Booking requests: guests and customers only, never staff.
Route::post('/packages/{package}/book', [BookingController::class, 'store'])
    ->middleware(['book', 'throttle:forms'])->name('bookings.store');
Route::get('/booking/{booking}', [BookingController::class, 'show'])->name('bookings.show');

// Prices in Indian or Nepali rupees, remembered for the visitor.
Route::get('/currency/{code}', [PageController::class, 'currency'])->whereIn('code', ['inr', 'npr'])->name('currency');

// Content
Route::get('/contact-us', [PageController::class, 'contact'])->name('contact');
Route::post('/enquiry', [PageController::class, 'inquire'])->middleware('throttle:forms')->name('inquiry.store');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');
Route::get('/search', [PageController::class, 'search'])->name('search');

// Search engines and AI assistants
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [SeoController::class, 'llms'])->name('llms');

// Customer accounts
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');

    // Forgot password: a link by email, then a new password.
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->middleware('throttle:auth')->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->middleware('throttle:auth')->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/my-account', [AccountController::class, 'index'])->name('account.index');
    Route::put('/my-account', [AccountController::class, 'update'])->name('account.update');
    Route::get('/my-account/bookings/{booking}', [AccountController::class, 'booking'])->name('account.booking');
});

// CMS pages last, so they never shadow a real route.
Route::get('/{page}', [PageController::class, 'show'])
    ->where('page', '(?!admin$|livewire|filament|storage|up$)[a-z0-9-]+')
    ->name('pages.show');
