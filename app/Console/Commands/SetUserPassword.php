<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Last-resort recovery on the server, for when the reset email cannot be
 * received (for example before email sending is set up in Site Settings).
 */
#[Signature('user:password {email : The account to reset} {--password= : The new password (at least 8 characters); a random one is made if left out}')]
#[Description('Set a new password for an account, without email')]
class SetUserPassword extends Command
{
    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('No account with that email address.');

            return self::FAILURE;
        }

        $password = $this->option('password') ?: Str::password(14, symbols: false);

        if (mb_strlen($password) < 8) {
            $this->error('The password must be at least 8 characters.');

            return self::FAILURE;
        }

        // The model hashes the password; a new remember token signs out other devices.
        $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();

        $this->info("New password for {$user->email}: {$password}");
        $this->line('Sign in with it, then change it from your profile.');

        return self::SUCCESS;
    }
}
