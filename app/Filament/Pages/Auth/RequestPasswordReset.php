<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\PasswordReset\RequestPasswordReset as BaseRequestPasswordReset;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Password;

/**
 * "Forgot password?" on the admin login page. Same as Filament's, except that an
 * unknown email gets the same "link sent" answer as a known one, so the form
 * cannot be used to find out which addresses have accounts.
 */
class RequestPasswordReset extends BaseRequestPasswordReset
{
    protected function getFailureNotification(string $status): ?Notification
    {
        if ($status === Password::INVALID_USER) {
            return $this->getSentNotification(Password::RESET_LINK_SENT);
        }

        return parent::getFailureNotification($status);
    }
}
