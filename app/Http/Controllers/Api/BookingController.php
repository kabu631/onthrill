<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\BookingReceived;
use App\Models\Booking;
use App\Models\Package;
use App\Support\Present;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

/**
 * Booking requests from a package page. Nothing is charged here: the team
 * confirms the details by phone and the traveller then pays the advance.
 */
class BookingController extends Controller
{
    public function store(Request $request, Package $package): JsonResponse
    {
        abort_unless($package->is_active, 404);

        $data = $request->validate([
            'travel_date' => ['required', 'date', 'after_or_equal:today'],
            'adults' => ['required', 'integer', 'min:1', 'max:50'],
            'children' => ['nullable', 'integer', 'min:0', 'max:30'],
            'nationality' => ['required', Rule::in(array_keys(Booking::NATIONALITIES))],
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'regex:/^[0-9+\-\s()]{7,20}$/'],
            'email' => ['required', 'email', 'max:160'],
            'travelling_from' => ['nullable', 'string', 'max:120'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [
            'travel_date.after_or_equal' => 'Please choose a date from today onwards.',
            'phone.regex' => 'Please enter a valid phone number.',
        ]);

        $currency = Booking::currencyFor($data['nationality']);
        $perPerson = $package->price($currency);
        $travellers = (int) $data['adults'] + (int) ($data['children'] ?? 0);
        $total = $perPerson !== null ? $perPerson * $travellers : null;

        $booking = Booking::create([
            ...$data,
            'children' => (int) ($data['children'] ?? 0),
            'reference' => Booking::nextReference(),
            'user_id' => $request->user()?->id,
            'package_id' => $package->id,
            'package_name' => $package->name,
            'currency' => $currency,
            'price_per_person' => $perPerson,
            'total' => $total,
            'advance' => $total !== null ? round($total * (float) setting('advance_percent') / 100) : null,
            'status' => 'new',
            'payment_status' => 'pending',
        ]);

        // The booking page is only shown to whoever made the booking.
        $request->session()->push('my_bookings', $booking->id);

        $this->notify($booking);

        return response()->json(['reference' => $booking->reference, 'url' => route('bookings.show', $booking, false)], 201);
    }

    public function show(Request $request, Booking $booking): JsonResponse
    {
        $madeHere = in_array($booking->id, $request->session()->get('my_bookings', []), true);
        $owner = $request->user() && $booking->user_id === $request->user()->id;
        abort_unless($madeHere || $owner, 404);

        return response()->json(['booking' => Present::booking($booking->load('package'))]);
    }

    /** Mail problems are logged, never shown to the traveller: the booking is already saved. */
    private function notify(Booking $booking): void
    {
        try {
            Mail::to($booking->email)->send(new BookingReceived($booking));
        } catch (Throwable $e) {
            Log::error('Booking confirmation email failed', ['booking' => $booking->reference, 'error' => $e->getMessage()]);
        }

        try {
            if ($admin = setting('booking_notify_email')) {
                Mail::to($admin)->send(new BookingReceived($booking, forAdmin: true));
            }
        } catch (Throwable $e) {
            Log::error('Booking notification failed', ['booking' => $booking->reference, 'error' => $e->getMessage()]);
        }
    }
}
