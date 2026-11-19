<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;

class BookingPolicy extends AreaPolicy
{
    protected string $area = 'bookings';

    /** Travellers may see their own bookings; staff need the bookings.view permission. */
    public function view(User $user, $booking): bool
    {
        return ($booking instanceof Booking && $booking->user_id === $user->id) || $this->allows($user, 'view');
    }

    /** Bookings only ever come from the website. */
    public function create(User $user): bool
    {
        return false;
    }

    public function replicate(User $user, $model): bool
    {
        return false;
    }
}
