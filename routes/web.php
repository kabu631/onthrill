<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\ContentController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\ShellController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| JSON API, read by the Angular website (frontend/)
|--------------------------------------------------------------------------
| Same origin and the same session cookie as the pages, so signing in and
| the CSRF check work exactly as before (Angular sends X-XSRF-TOKEN).
*/
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/site', [CatalogController::class, 'site'])->name('site');
    Route::get('/home', [CatalogController::class, 'home'])->name('home');
    Route::get('/destinations', [CatalogController::class, 'destinations'])->name('destinations');
    Route::get('/destinations/{destination}', [CatalogController::class, 'destination'])->name('destination');
    Route::get('/packages', [CatalogController::class, 'packages'])->name('packages');
    Route::get('/packages/{package}', [CatalogController::class, 'package'])->name('package');

    // Booking requests: guests and customers only, never staff.
    Route::post('/packages/{package}/book', [BookingController::class, 'store'])->middleware(['book', 'throttle:forms'])->name('book');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('booking');

    Route::get('/pages/{page}', [ContentController::class, 'page'])->name('page');
    Route::get('/gallery', [ContentController::class, 'gallery'])->name('gallery');
    Route::get('/search', [ContentController::class, 'search'])->name('search');
    Route::get('/contact', [ContentController::class, 'contact'])->name('contact');
    Route::post('/enquiry', [ContentController::class, 'enquire'])->middleware('throttle:forms')->name('enquiry');

    Route::middleware('throttle:auth')->group(function () {
        Route::post('/login', [AccountController::class, 'login'])->name('login');
        Route::post('/register', [AccountController::class, 'register'])->name('register');
        Route::post('/forgot-password', [AccountController::class, 'forgotPassword'])->name('password.email');
        Route::post('/reset-password', [AccountController::class, 'resetPassword'])->name('password.update');
    });
    Route::post('/logout', [AccountController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/account', [AccountController::class, 'show'])->name('account');
        Route::put('/account', [AccountController::class, 'update'])->name('account.update');
        Route::get('/account/bookings/{booking}', [AccountController::class, 'booking'])->name('account.booking');
    });

    Route::any('/{any}', fn () => abort(404))->where('any', '.*');
});

/*
|--------------------------------------------------------------------------
| Website pages (drawn by the Angular app, see ShellController)
|--------------------------------------------------------------------------
*/
Route::get('/', [ShellController::class, 'home'])->name('home');

Route::get('/destinations', [ShellController::class, 'destinations'])->name('destinations.index');
Route::get('/destinations/country/{country}', [ShellController::class, 'destinations'])
    ->whereIn('country', ['nepal', 'india', 'tibet'])->name('destinations.country');
Route::get('/destinations/{destination}', [ShellController::class, 'destination'])->name('destinations.show');

Route::get('/packages', [ShellController::class, 'packages'])->name('packages.index');
Route::get('/packages/type/{category}', [ShellController::class, 'packages'])->name('packages.category');
Route::get('/packages/{package}', [ShellController::class, 'package'])->name('packages.show');
Route::get('/booking/{booking}', [ShellController::class, 'booking'])->name('bookings.show');

Route::get('/contact-us', [ShellController::class, 'contact'])->name('contact');
Route::get('/gallery', [ShellController::class, 'gallery'])->name('gallery');
Route::get('/search', [ShellController::class, 'search'])->name('search');

// Search engines and AI assistants
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/llms.txt', [SeoController::class, 'llms'])->name('llms');

// Customer accounts
Route::middleware('guest')->group(function () {
    Route::get('/login', [ShellController::class, 'form'])->defaults('titleKey', 'login_title')->name('login');
    Route::get('/register', [ShellController::class, 'form'])->defaults('titleKey', 'register_title')->name('register');
    Route::get('/forgot-password', [ShellController::class, 'form'])->defaults('titleKey', 'forgot_title')->name('password.request');
    Route::get('/reset-password/{token}', [ShellController::class, 'form'])->defaults('titleKey', 'reset_title')->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('/my-account', [ShellController::class, 'account'])->name('account.index');
    Route::get('/my-account/bookings/{booking}', [ShellController::class, 'accountBooking'])->name('account.booking');
});

// Signing out from the admin panel's "not allowed" page (a plain form, not the app).
Route::post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return $request->input('next') === 'admin-login' ? redirect('/admin/login') : redirect()->route('home');
})->name('logout');

// CMS pages last, so they never shadow a real route.
Route::get('/{page}', [ShellController::class, 'page'])
    ->where('page', '(?!admin$|livewire|filament|storage|app$|up$)[a-z0-9-]+')
    ->name('pages.show');
