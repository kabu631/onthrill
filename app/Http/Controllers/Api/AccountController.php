<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\User;
use App\Support\Access;
use App\Support\Present;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

/**
 * Customer accounts: sign in, register, forgotten passwords and "My Account".
 * Sessions are the same as before (cookie based, until the browser closes);
 * staff sign in to the admin panel at /admin.
 */
class AccountController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Never "remember me": signing in lasts until the browser closes (config/session.php).
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'These credentials do not match our records.']);
        }

        // Keep bookings made before signing in (kept in the session) across the session-id change.
        $request->session()->regenerate();

        return $this->signedIn($request);
    }

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ], ['phone.regex' => 'Please enter a valid phone number.']);

        $user = User::create($data);
        // Everyone who signs up on the website is a customer; staff roles are
        // only ever granted from the admin panel.
        $user->assignRole(Access::CUSTOMER);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->signedIn($request, 'Welcome to '.setting('site_name').'!');
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['user' => null, 'can_book' => true]);
    }

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_THROTTLED) {
            throw ValidationException::withMessages(['email' => 'A link was sent to this address a moment ago. Please wait a minute before asking again.']);
        }

        // The same answer whether or not the address has an account, so this form
        // cannot be used to find out who is registered.
        return response()->json(['message' => 'If an account exists for '.$request->input('email').', we have emailed it a link to choose a new password. Please check your inbox, and the spam folder too.']);
    }

    public function resetPassword(Request $request): JsonResponse
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
            throw ValidationException::withMessages(['email' => $status === Password::INVALID_TOKEN
                ? 'This link has expired or has already been used. Please ask for a new one.'
                : 'We could not find an account with that email address.']);
        }

        Auth::login(User::where('email', $request->input('email'))->firstOrFail());
        $request->session()->regenerate();

        return $this->signedIn($request, 'Your password has been changed and you are signed in.');
    }

    public function show(Request $request): JsonResponse
    {
        return response()->json([
            'user' => Present::user($request->user()),
            'bookings' => Present::paginated($request->user()->bookings()->paginate(10), fn (Booking $b) => [
                'reference' => $b->reference,
                'package_name' => $b->package_name,
                'travel_date' => $b->travel_date?->format('d M Y'),
                'travellers' => $b->travellers(),
                'status_label' => $b->statusLabel(),
                'payment_label' => Booking::PAYMENT_STATUSES[$b->payment_status] ?? $b->payment_status,
                'is_paid' => $b->isPaid(),
                'currency' => $b->currency,
                'total' => $b->total !== null ? (float) $b->total : null,
            ]),
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', PasswordRule::min(8)],
        ], ['phone.regex' => 'Please enter a valid phone number.']);

        $user->fill(collect($data)->only(['name', 'email', 'phone'])->all());

        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return response()->json(['user' => Present::user($user), 'message' => 'Your details have been updated.']);
    }

    public function booking(Request $request, Booking $booking): JsonResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 404);

        return response()->json(['booking' => Present::booking($booking->load('package'))]);
    }

    /**
     * Where to go next: back to the page the visitor was trying to open, or else
     * staff to the admin panel and customers to their account. A customer is never
     * sent to the admin panel (it would only show them a "not allowed" page).
     */
    private function signedIn(Request $request, ?string $message = null): JsonResponse
    {
        $user = $request->user();
        $intended = $request->session()->pull('url.intended');
        $path = $intended ? (string) parse_url($intended, PHP_URL_PATH) : null;

        if ($path && ! $user->isStaff() && preg_match('#^/admin(/|$)#', $path)) {
            $path = null;
        }
        if ($path && ($query = parse_url($intended, PHP_URL_QUERY))) {
            $path .= '?'.$query;
        }

        return response()->json([
            'user' => Present::user($user),
            'can_book' => Gate::allows('book'),
            'redirect' => $path ?: ($user->isStaff() ? '/admin' : '/my-account'),
            'message' => $message,
        ]);
    }
}
