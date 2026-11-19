<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\Hidden;
use Filament\Schemas\Components\Component;

/**
 * Admin login without "Remember me": signing in lasts until the browser is
 * closed (or the PC restarts), like the website's sign-in. See config/session.php.
 */
class Login extends BaseLogin
{
    protected function getRememberFormComponent(): Component
    {
        // Always false, whatever the browser sends.
        return Hidden::make('remember')->default(false)->dehydrateStateUsing(fn () => false);
    }
}
