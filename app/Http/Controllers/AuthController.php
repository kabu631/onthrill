<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\Access;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

/** Customer sign-in and registration. Admins sign in at /admin. */
class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Never "remember me": signing in lasts until the browser closes (config/session.php).
        if (! Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
        }

        // Keep bookings made before signing in (kept in the session) across the session-id change.
        $request->session()->regenerate();

        return $this->afterSignIn($request);
    }

    /**
     * Back to the page the visitor was trying to open, or else staff to the admin
     * panel and customers to their account. A customer is never sent to the admin
     * panel: if they opened /admin earlier in this browser (which remembers it),
     * signing in or registering as a customer must not end on a "403" page.
     */
    private function afterSignIn(Request $request): RedirectResponse
    {
        $user = $request->user();
        $intended = $request->session()->pull('url.intended');

        if ($intended && ! $user->isStaff() && preg_match('#^/admin(/|$)#', (string) parse_url($intended, PHP_URL_PATH))) {
            $intended = null;
        }

        return redirect()->to($intended ?: ($user->isStaff() ? '/admin' : route('account.index')));
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], ['phone.regex' => 'Please enter a valid phone number.']);

        $user = User::create($data);
        // Everyone who signs up on the website is a customer; staff roles are
        // only ever granted from the admin panel.
        $user->assignRole(Access::CUSTOMER);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->afterSignIn($request)->with('success', 'Welcome to '.setting('site_name').'!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // "Sign out" on the admin panel's "not allowed" page leads to the admin login.
        return $request->input('next') === 'admin-login' ? redirect('/admin/login') : redirect()->route('home');
    }
}
