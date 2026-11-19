<x-layout :title="wording('account_title')" noindex>
    <x-page-banner :title="wording('account_title')" :crumbs="[wording('account_title') => null]" />

    <div class="container-page section pt-8 sm:pt-10">
        <div class="grid gap-8 lg:grid-cols-[1fr_22rem]">
            <section data-reveal>
                <h2 class="text-2xl font-bold">{{ wording('account_bookings') }}</h2>

                @if ($bookings->isEmpty())
                    <div class="card mt-5 p-8 text-center">
                        <p>{{ wording('account_empty') }}</p>
                        <a href="{{ route('packages.index') }}" class="btn btn-primary mt-4">{{ wording('account_browse') }}</a>
                    </div>
                @else
                    <ul class="mt-5 space-y-3">
                        @foreach ($bookings as $booking)
                            <li>
                                <a href="{{ route('account.booking', $booking) }}" class="card flex flex-wrap items-center justify-between gap-3 p-5 transition-colors hover:border-saffron-400">
                                    <span>
                                        <span class="block font-semibold text-ink">{{ $booking->package_name }}</span>
                                        <span class="text-xs text-ink/55">{{ $booking->reference }} · {{ $booking->travel_date?->format('d M Y') }} · {{ $booking->travellers() }} {{ \Illuminate\Support\Str::plural('traveller', $booking->travellers()) }}</span>
                                    </span>
                                    <span class="flex items-center gap-3">
                                        <span @class(['badge', 'bg-green-100 text-green-800' => $booking->isPaid(), 'bg-amber-100 text-amber-800' => ! $booking->isPaid()])>{{ \App\Models\Booking::PAYMENT_STATUSES[$booking->payment_status] ?? $booking->payment_status }}</span>
                                        <span class="badge bg-navy-100 text-navy-800">{{ $booking->statusLabel() }}</span>
                                        @if ($booking->total !== null)
                                            <span class="font-bold text-navy-800">{{ $booking->money($booking->total) }}</span>
                                        @endif
                                        <x-op-icon name="chevron-right" class="size-4 text-ink/40" />
                                    </span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    {{ $bookings->links() }}
                @endif
            </section>

            <aside data-reveal class="space-y-6">
                <div class="card p-6">
                    <h2 class="text-xl font-bold">{{ wording('account_details') }}</h2>
                    <form action="{{ route('account.update') }}" method="post" class="mt-5 space-y-4">
                        @csrf @method('PUT')
                        <x-field name="name" :label="wording('field_full_name')" required :value="$user->name" maxlength="120" />
                        <x-field name="email" :label="wording('field_email')" type="email" required :value="$user->email" maxlength="160" />
                        <x-field name="phone" :label="wording('field_phone')" type="tel" :value="$user->phone" maxlength="20" />

                        <details class="rounded-md border border-ink/10 p-4 text-sm" @if ($errors->hasAny(['password', 'current_password'])) open @endif>
                            <summary class="cursor-pointer font-medium text-ink">{{ wording('account_change_password') }}</summary>
                            <div class="mt-4 space-y-4">
                                <x-field name="current_password" :label="wording('field_current_password')" type="password" autocomplete="current-password" />
                                <x-field name="password" :label="wording('field_new_password')" type="password" autocomplete="new-password" />
                                <x-field name="password_confirmation" :label="wording('field_confirm_password')" type="password" autocomplete="new-password" />
                            </div>
                        </details>

                        <button type="submit" class="btn btn-primary w-full">{{ wording('account_save') }}</button>
                    </form>
                </div>

                @if ($user->isStaff())
                    <a href="{{ url('/admin') }}" class="btn btn-dark w-full"><x-op-icon name="shield" class="size-4" /> Open Admin Panel</a>
                @endif

                <form action="{{ route('logout') }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-outline w-full"><x-op-icon name="logout" class="size-4" /> {{ wording('account_sign_out') }}</button>
                </form>
            </aside>
        </div>
    </div>
</x-layout>
