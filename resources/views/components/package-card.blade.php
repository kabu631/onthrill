@props(['package', 'currency'])

{{-- A tour package: photo with the trip length, the places it visits, key facts and the price. --}}
@php
    $p = $package;
    $places = $p->relationLoaded('destinations') ? $p->destinations : collect();
    $placeLine = $places->isNotEmpty()
        ? $places->take(3)->pluck('name')->implode(', ').($places->count() > 3 ? ' +'.($places->count() - 3) : '')
        : $p->start_point;
    $bookable = Gate::allows('book');
@endphp

<article class="pkg-card card group flex h-full flex-col overflow-hidden transition-colors duration-300 hover:border-saffron-300">
    <a href="{{ $p->url() }}" class="pkg-card-media relative block aspect-[4/3] overflow-hidden bg-mist" tabindex="-1" aria-hidden="true">
        <x-media :path="$p->image" :width="400" :alt="$p->name" class="absolute inset-0" />
        <span class="badge absolute top-3 left-3 z-10 bg-white/95 text-navy-900"><x-op-icon name="clock" class="size-3.5 text-saffron-600" /> {{ $p->durationLabel() }}</span>
        @if ($p->isOnSale($currency))
            <span class="badge absolute top-3 right-3 z-10 bg-saffron-600 text-white">{{ wording('price_off', ['percent' => $p->discountPercent($currency)]) }}</span>
        @endif
    </a>

    <div class="flex flex-1 flex-col p-5">
        @if ($placeLine)
            <p class="flex items-start gap-1.5 text-xs text-ink/60">
                <x-op-icon name="map-pin" class="mt-px size-3.5 shrink-0 text-saffron-600" />
                <span class="line-clamp-1">{{ $placeLine }}</span>
            </p>
        @endif
        <h3 class="mt-2 line-clamp-2 text-lg leading-snug font-semibold">
            <a href="{{ $p->url() }}" class="transition-colors hover:text-saffron-600">{{ $p->name }}</a>
        </h3>
        @if ($p->summary)
            {{-- Shown in the list view of the packages page --}}
            <p class="pkg-card-summary mt-2 line-clamp-2 text-sm leading-relaxed">{{ $p->summary }}</p>
        @endif

        <ul class="mt-3 flex flex-wrap gap-x-4 gap-y-1.5 text-xs text-ink/65">
            @if ($p->category)
                <li class="flex items-center gap-1.5"><x-op-icon name="map" class="size-3.5 text-navy-400" /> {{ $p->category->name }}</li>
            @endif
            @if ($p->group_size)
                <li class="flex items-center gap-1.5"><x-op-icon name="users" class="size-3.5 text-navy-400" /> {{ $p->group_size }}</li>
            @endif
            @if ($p->max_altitude_m)
                <li class="flex items-center gap-1.5"><x-op-icon name="mountain" class="size-3.5 text-navy-400" /> {{ number_format($p->max_altitude_m) }} m</li>
            @endif
        </ul>

        <div class="mt-auto pt-5">
            <div class="flex items-end justify-between gap-3 border-t border-navy-100 pt-4">
                <x-price :package="$p" :currency="$currency" />
                <a href="{{ $p->url() }}" class="btn btn-sm shrink-0 border border-saffron-600 text-saffron-700 hover:bg-saffron-600 hover:text-white">
                    {{ $p->isOnRequest() && $bookable ? wording('card_enquire') : wording('card_view') }}
                </a>
            </div>
        </div>
    </div>
</article>
