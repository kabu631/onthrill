<?php

namespace App\Providers;

use App\Models\Destination;
use App\Models\GalleryImage;
use App\Models\Package;
use App\Models\Page;
use App\Models\Testimonial;
use App\Models\TourCategory;
use App\Models\User;
use App\Notifications\ResetPasswordLink;
use App\Policies\RolePolicy;
use App\Support\Menu;
use App\Support\Settings;
use App\Support\Thumbnails;
use Filament\Auth\Notifications\ResetPassword;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Spatie\Permission\Models\Role;

use function Illuminate\Support\defer;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // The admin panel's "Forgot password?" sends the site's own reset email
        // (same wording as the website's, and sent at once rather than queued).
        $this->app->bind(ResetPassword::class, ResetPasswordLink::class);
    }

    public function boot(): void
    {
        // Policies for App\Models are auto-discovered from App\Policies; the Role
        // model lives in a package, so its policy is registered by hand.
        Gate::policy(Role::class, RolePolicy::class);

        // Booking is for travellers: guests and customer accounts. Staff accounts
        // (any admin role) manage bookings but never make them.
        Gate::define('book', fn (?User $user) => $user === null || ! $user->isStaff());

        Thumbnails::resetRequestBudget();

        // Right-sized copies of newly uploaded images are made after the admin's
        // save has been answered, so saving never waits for image processing.
        $imageFields = [
            Destination::class => ['image', 'gallery'],
            Package::class => ['image', 'gallery'],
            TourCategory::class => ['image'],
            GalleryImage::class => ['image'],
            Testimonial::class => ['photo'],
        ];
        foreach ($imageFields as $model => $fields) {
            $model::saved(function ($record) use ($fields) {
                $paths = collect($fields)
                    ->filter(fn ($field) => $record->wasChanged($field) || $record->wasRecentlyCreated)
                    ->flatMap(fn ($field) => (array) $record->{$field})
                    ->filter(fn ($path) => is_string($path) && $path !== '');

                if ($paths->isNotEmpty()) {
                    defer(fn () => $paths->each(fn ($path) => Thumbnails::generateAll($path)));
                }
            });
        }

        // Outgoing mail uses the SMTP server saved in Admin > Site Settings > Email.
        Settings::applyMailConfig();

        // Keep the cached navigation in sync with the content.
        foreach ([Destination::class, TourCategory::class, Package::class, Page::class] as $model) {
            $model::saved(fn () => Menu::flush());
            $model::deleted(fn () => Menu::flush());
        }

        RateLimiter::for('forms', fn (Request $request) => Limit::perMinute(5)->by($request->ip()));
        RateLimiter::for('auth', fn (Request $request) => Limit::perMinute(8)->by($request->ip()));
    }
}
