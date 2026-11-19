<?php

namespace App\Models;

use App\Notifications\ResetPasswordLink;
use App\Support\Access;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'phone', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole(Access::SUPER_ADMIN);
    }

    /** Staff = anyone with at least one admin permission. Customers have none. */
    public function isStaff(): bool
    {
        return $this->isSuperAdmin() || $this->getAllPermissions()->isNotEmpty();
    }

    /** Permission check that treats Super Admin as having everything. */
    public function allows(string $permission): bool
    {
        return $this->isSuperAdmin() || $this->checkPermissionTo($permission);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isStaff();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class)->latest();
    }

    /** "Forgot password?" on the website: the email links to the website's reset page. */
    public function sendPasswordResetNotification($token): void
    {
        $notification = new ResetPasswordLink($token);
        $notification->url = route('password.reset', ['token' => $token, 'email' => $this->getEmailForPasswordReset()]);

        $this->notify($notification);
    }
}
