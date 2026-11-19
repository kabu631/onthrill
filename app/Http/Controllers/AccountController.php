<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AccountController extends Controller
{
    public function index(Request $request)
    {
        return view('account.index', [
            'user' => $request->user(),
            'bookings' => $request->user()->bookings()->paginate(10),
        ]);
    }

    public function booking(Request $request, Booking $booking)
    {
        abort_unless($booking->user_id === $request->user()->id, 404);

        return view('bookings.show', ['booking' => $booking->load('package'), 'inAccount' => true]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', Password::min(8)],
        ], ['phone.regex' => 'Please enter a valid phone number.']);

        $user->fill(collect($data)->only(['name', 'email', 'phone'])->all());

        if (filled($data['password'] ?? null)) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()->with('success', 'Your details have been updated.');
    }
}
