@props(['title', 'crumbs' => [], 'intro' => null, 'image' => null, 'credit' => null, 'creditUrl' => null, 'tall' => false])

{{--
    Page title over a photograph, with breadcrumbs. $crumbs: [label => url|null].
    $image replaces the default banner photo (e.g. a destination's own photo);
    $credit is the photographer credit that the photo's licence requires.
    $tall gives destination pages a larger photo.
--}}
@php
    $banner = $image ?: media_url(setting('page_banner'));
@endphp

@push('schema')
    <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs($crumbs)) !!}</script>
@endpush

<section @class(['relative isolate flex items-center overflow-hidden bg-navy-950 text-center', 'min-h-[18rem] sm:min-h-[22rem]' => ! $tall, 'min-h-[22rem] sm:min-h-[30rem]' => $tall])>
    @if ($banner)
        <img src="{{ $banner }}" alt="" class="absolute inset-0 -z-20 size-full object-cover object-center" fetchpriority="high">
    @endif
    {{-- Darkened so the white title always reads well over the photo. --}}
    <div class="absolute inset-0 -z-10 bg-navy-950/55" aria-hidden="true"></div>

    <div class="container-page py-14 sm:py-20">
        <h1 class="anim-rise mx-auto max-w-4xl text-3xl leading-tight font-bold text-white sm:text-5xl">{{ $title }}</h1>
        @if ($intro)
            <p style="--d: 100ms" class="anim-rise mx-auto mt-4 max-w-2xl text-sm leading-relaxed text-white/85 sm:text-base">{{ $intro }}</p>
        @endif
        {{ $slot ?? '' }}

        <nav aria-label="Breadcrumb" style="--d: 180ms" class="anim-rise mt-6 inline-flex flex-wrap items-center justify-center gap-x-2 gap-y-1 rounded-md bg-navy-950/45 px-4 py-2 text-xs text-white/80 sm:text-sm">
            <a href="{{ route('home') }}" class="hover:text-white">{{ wording('breadcrumb_home') }}</a>
            @foreach ($crumbs as $label => $url)
                <x-op-icon name="chevron-right" class="size-3 text-white/50" />
                @if ($url)
                    <a href="{{ $url }}" class="hover:text-white">{{ $label }}</a>
                @else
                    <span class="font-medium text-saffron-300" aria-current="page">{{ $label }}</span>
                @endif
            @endforeach
        </nav>
    </div>

    @if ($credit)
        <p class="absolute top-2 right-3 max-w-[60%] truncate text-[10px] text-white/55">
            @if ($creditUrl)<a href="{{ $creditUrl }}" target="_blank" rel="noopener" class="hover:text-white">{{ $credit }}</a>@else{{ $credit }}@endif
        </p>
    @endif
</section>
