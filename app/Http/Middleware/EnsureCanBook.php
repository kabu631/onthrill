<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards booking (the "book" ability, defined in AppServiceProvider): staff
 * accounts are turned away with a clear message instead of a bare 403 page.
 */
class EnsureCanBook
{
    public const MESSAGE = 'You are signed in with a staff account. Staff accounts cannot make bookings. Sign out, or sign in with a customer account.';

    public function handle(Request $request, Closure $next): Response
    {
        if (Gate::allows('book')) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => self::MESSAGE], 403);
        }

        return back()->with('error', self::MESSAGE);
    }
}
