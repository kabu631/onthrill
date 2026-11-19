@props(['destination', 'size' => 400])

{{--
    A destination as a photo card with its name over the bottom of the photo.
    Shows the number of tours when the query counted them (withCount packages).
--}}
@php
    $d = $destination;
    $region = \Illuminate\Support\Str::before((string) $d->region, ',');
    $tours = $d->packages_count ?? null;
@endphp

<a href="{{ $d->url() }}" {{ $attributes->class(['group relative isolate block aspect-[4/5] overflow-hidden rounded-lg bg-navy-900']) }}>
    <x-media :path="$d->image" :width="$size" :alt="$d->name" class="absolute inset-0 -z-20" />
    {{-- Darkens the bottom of the photo so the white text reads on any image --}}
    <span class="absolute inset-0 -z-10 bg-linear-to-t from-navy-950/90 via-navy-950/25 to-transparent" aria-hidden="true"></span>

    <span class="badge absolute top-3 left-3 bg-white/95 text-navy-900">{{ \Illuminate\Support\Str::before($d->country, ' (') }}</span>

    <span class="absolute inset-x-0 bottom-0 block p-5">
        <span class="block font-display text-xl leading-snug font-semibold text-white sm:text-2xl">{{ $d->name }}</span>
        <span class="mt-1 block text-sm text-white/80">{{ $region }}@if ($d->altitude_m) · {{ number_format($d->altitude_m) }} m @endif</span>
        <span class="mt-3 flex items-center gap-1.5 text-sm font-semibold text-saffron-300">
            @if ($tours)
                {{ wording($tours === 1 ? 'card_tours_one' : 'card_tours_many', ['count' => $tours]) }}
            @else
                {{ wording('card_about') }}
            @endif
            <x-op-icon name="arrow-right" class="size-4" />
        </span>
    </span>
</a>
