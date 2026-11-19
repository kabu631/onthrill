<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;

/**
 * The "choose a new password" email, for customers (website) and staff (admin
 * panel). The website sets $url to its own reset page (User::sendPasswordResetNotification);
 * the admin panel builds this same class (bound in AppServiceProvider) and sets
 * $url to the admin reset page. Sent straight away, never queued, because the
 * person is waiting for it.
 */
class ResetPasswordLink extends ResetPassword
{
    public string $url = '';

    protected function resetUrl($notifiable): string
    {
        return $this->url;
    }

    public function toMail($notifiable): MailMessage
    {
        $minutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');
        $site = setting('site_name');

        return (new MailMessage)
            ->subject("Reset your password | {$site}")
            ->greeting('Namaste '.Str::of($notifiable->name)->before(' ').',')
            ->line("We received a request to reset the password for your {$site} account ({$notifiable->getEmailForPasswordReset()}).")
            ->action('Choose a new password', $this->resetUrl($notifiable))
            ->line("The link works once, for the next {$minutes} minutes.")
            ->line('If you did not ask for this, you can ignore this email. Your password stays the same.')
            // A blank line, so the name sits on its own line in both the HTML and plain-text versions.
            ->salutation("Jai Shri Ram,\n\n{$site}");
    }
}
