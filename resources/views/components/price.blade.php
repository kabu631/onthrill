@props(['package', 'currency', 'size' => 'base'])

{{-- Per-person price of a package in the visitor's currency: offer / regular / on request. --}}
@php
    $main = $size === 'lg' ? 'text-3xl' : 'text-lg';
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-wrap items-baseline gap-x-2 gap-y-0.5']) }}>
    @if ($package->isOnRequest())
        <span class="{{ $size === 'lg' ? 'text-2xl' : 'text-base' }} font-semibold text-navy-800">{{ wording('price_on_request') }}</span>
    @else
        @if ($size !== 'lg')
            <span class="text-xs text-ink/55">{{ wording('price_from') }}</span>
        @endif
        <span class="{{ $main }} font-bold text-navy-800">{{ money($package->price($currency), $currency) }}</span>
        @if ($package->isOnSale($currency))
            <span class="text-sm text-ink/45 line-through">{{ money($package->regularPrice($currency), $currency) }}</span>
        @endif
        <span class="text-xs text-ink/55">{{ wording('price_per_person') }}</span>
    @endif
</div>
