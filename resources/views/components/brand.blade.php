@props(['light' => false])

{{--
    The site's name and tagline as a wordmark, or the uploaded logo when there is one
    (Admin > Site Settings > General). $light is for dark backgrounds (footer).
--}}
@php
    $logo = media_url(setting($light ? 'logo_footer' : 'logo'));
@endphp

@if ($logo)
    <img src="{{ $logo }}" alt="{{ setting('site_name') }}" {{ $attributes->merge(['class' => 'h-9 w-auto lg:h-10']) }}>
@else
    <span {{ $attributes->merge(['class' => 'flex items-center gap-2.5']) }}>
        <svg class="size-9 shrink-0 lg:size-10" viewBox="0 0 64 64" aria-hidden="true">
            <rect width="64" height="64" rx="12" fill="{{ $light ? '#ff8335' : '#13293a' }}"/>
            <path d="M14 46 28 22l7 11 5-7 10 20z" fill="{{ $light ? '#13293a' : '#ff8335' }}"/>
            <circle cx="44" cy="18" r="4" fill="{{ $light ? '#fff6ef' : '#ffd2b5' }}"/>
        </svg>
        <span class="leading-none">
            <span @class(['block font-display text-2xl font-bold tracking-tight lg:text-[1.7rem]', 'text-white' => $light, 'text-navy-900' => ! $light])>{{ setting('site_name') }}</span>
            @if (filled(setting('tagline')))
                <span @class(['mt-1 block text-[11px] font-medium tracking-wide', 'text-saffron-300' => $light, 'text-saffron-700' => ! $light])>{{ setting('tagline') }}</span>
            @endif
        </span>
    </span>
@endif
