<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

/**
 * "Forgot password?" on the website: ask for a link by email, then choose a
 * new password. Works for customers and staff (the admin login page has its
 * own form that sends the same email).
 */
class PasswordResetController extends Controller
{
    public function request()
    {
        return view('auth.forgot-password');
    }

    public function email(Request $request)
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors(['email' => 'A link was sent to this address a moment ago. Please wait a minute before asking again.'])->onlyInput('email');
        }

        // The same answer whether or not the address has an account, so this form
        // cannot be used to find out who is registered.
        return back()->with('status', 'If an account exists for '.$request->input('email').', we have emailed it a link to choose a new password. Please check your inbox, and the spam folder too.');
    }

    public function edit(Request $request, string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => (string) $request->query('email')]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                // The model hashes the password; a new remember token signs out other devices.
                $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
                event(new PasswordReset($user));
            },
        );

        if ($status !== Password::PASSWORD_RESET) {
            $message = $status === Password::INVALID_TOKEN
                ? 'This link has expired or has already been used. Please ask for a new one below.'
                : 'We could not find an account with that email address.';

            return $status === Password::INVALID_TOKEN
                ? redirect()->route('password.request')->withErrors(['email' => $message])->withInput($request->only('email'))
                : back()->withErrors(['email' => $message])->onlyInput('email');
        }

        $user = User::where('email', $request->input('email'))->firstOrFail();
        Auth::login($user);
        $request->session()->regenerate();

        return $user->isStaff()
            ? redirect('/admin')
            : redirect()->route('account.index')->with('success', 'Your password has been changed and you are signed in.');
    }
}
