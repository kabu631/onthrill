<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Access;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('access:sync {--super-admin= : Email of a user to make Super Admin}')]
#[Description('Create any missing roles and permissions; optionally promote a user to Super Admin')]
class SyncAccess extends Command
{
    public function handle(): int
    {
        Access::sync();
        $this->info('Roles and permissions are up to date.');

        if ($email = $this->option('super-admin')) {
            $user = User::where('email', $email)->first();

            if (! $user) {
                $this->error("No user with email {$email}.");

                return self::FAILURE;
            }

            $user->syncRoles([Access::SUPER_ADMIN]);
            $this->info("{$email} is now a Super Admin.");
        }

        return self::SUCCESS;
    }
}
