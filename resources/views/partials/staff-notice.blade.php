{{--
    Shown to staff accounts in place of the booking form (they lack the "book"
    ability, see AppServiceProvider). Optional: $editUrl.
--}}
<div class="card border-navy-300 bg-mist p-5">
    <p class="font-semibold text-ink">Booking is turned off for staff accounts</p>
    <p class="mt-1 text-sm">You are signed in as {{ auth()->user()->name }}, a staff account. To try a booking, sign out or use a customer account.</p>
    <div class="mt-4 flex flex-wrap gap-3">
        @if (! empty($editUrl))
            <a href="{{ $editUrl }}" class="btn btn-dark btn-sm">Edit in admin</a>
        @endif
        <form action="{{ route('logout') }}" method="post">
            @csrf
            <button type="submit" class="btn btn-outline btn-sm">Sign out</button>
        </form>
    </div>
</div>
