@php
    $b = $booking;
    $inAccount ??= false;
    $rows = array_filter([
        wording('bookingpage_row_tour') => $b->package ? '<a href="'.e($b->package->url()).'" class="font-medium text-saffron-700 underline underline-offset-2">'.e($b->package_name).'</a>' : e($b->package_name),
        wording('bookingpage_row_date') => e($b->travel_date?->format('l, d F Y')),
        wording('bookingpage_row_travellers') => e($b->adults.' '.\Illuminate\Support\Str::plural('adult', $b->adults).($b->children ? ', '.$b->children.' '.\Illuminate\Support\Str::plural('child', $b->children) : '')),
        wording('bookingpage_row_nationality') => e(\App\Models\Booking::NATIONALITIES[$b->nationality] ?? $b->nationality),
        wording('bookingpage_row_from') => $b->travelling_from ? e($b->travelling_from) : null,
        wording('bookingpage_row_total') => $b->total !== null ? e($b->money($b->total)).' <span class="text-ink/55">('.e($b->money($b->price_per_person)).' '.e(wording('price_per_person')).')</span>' : e(wording('price_on_request')),
        wording('bookingpage_row_status') => e($b->statusLabel()).' · '.e(\App\Models\Booking::PAYMENT_STATUSES[$b->payment_status] ?? $b->payment_status),
    ]);
@endphp

<x-layout :title="wording('bookingpage_title', ['reference' => $b->reference])" noindex>
    @if ($inAccount)
        <x-page-banner :title="wording('bookingpage_title', ['reference' => $b->reference])" :crumbs="[wording('account_title') => route('account.index'), $b->reference => null]" />
    @endif

    <div @class(['container-page section', 'pt-8 sm:pt-10' => $inAccount])>
        <div data-reveal class="mx-auto max-w-3xl">
            @unless ($inAccount)
                <div class="text-center">
                    <h1 class="text-3xl font-bold sm:text-4xl">{{ wording('bookingpage_thanks', ['name' => \Illuminate\Support\Str::of($b->name)->before(' ')]) }}</h1>
                    <p class="mt-3">{{ wording_html('bookingpage_received', ['reference' => $b->reference, 'email' => $b->email, 'phone' => $b->phone]) }}</p>
                </div>
            @endunless

            <div class="card mt-8 p-6">
                <h2 class="text-xl font-bold">{{ wording('bookingpage_details') }}</h2>
                <dl class="mt-4 divide-y divide-navy-100 text-sm">
                    @foreach ($rows as $label => $value)
                        <div class="grid gap-1 py-3 sm:grid-cols-[10rem_1fr]">
                            <dt class="text-navy-500">{{ $label }}</dt>
                            <dd class="text-ink">{!! $value !!}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            @if (! $b->isPaid() && $b->status !== 'cancelled')
                <div class="mt-6">
                    @include('partials.payment-box', ['booking' => $b])
                </div>
            @endif

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-outline">{{ wording('bookingpage_home') }}</a>
                @auth
                    <a href="{{ route('account.index') }}" class="btn btn-dark">{{ wording('bookingpage_account') }}</a>
                @endauth
            </div>
        </div>
    </div>
</x-layout>
