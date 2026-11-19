{{-- $overHero: the menu bar starts transparent over a full-width photo (homepage) and turns white on scroll. --}}
@props(['title' => null, 'description' => null, 'image' => null, 'noindex' => false, 'type' => 'website', 'overHero' => false])

@php
    $siteName = setting('site_name');
    $pageTitle = $title ? $title.' | '.$siteName : setting('meta_title');
    $pageDescription = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) ($description ?: setting('meta_description'))))), 160);
    $shareImage = $image ?: media_url(setting('share_image'));
    // Canonical URL: the page without tracking or filter parameters (pagination is kept).
    $canonical = url()->current().(request()->integer('page') > 1 ? '?page='.request()->integer('page') : '');
@endphp

<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $canonical }}">
    @if ($noindex)
        <meta name="robots" content="noindex, follow">
    @else
        <meta name="robots" content="index, follow, max-image-preview:large">
    @endif

    {{-- Sharing on WhatsApp, Facebook and X --}}
    <meta property="og:type" content="{{ $type }}">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="en_IN">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonical }}">
    @if ($shareImage)
        <meta property="og:image" content="{{ $shareImage }}">
        <meta name="twitter:image" content="{{ $shareImage }}">
    @endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">

    {{-- Where the business is (Admin > Site Settings > SEO) --}}
    @if (filled(setting('geo_region')))
        <meta name="geo.region" content="{{ setting('geo_region') }}">
    @endif
    @if (filled(setting('geo_placename')))
        <meta name="geo.placename" content="{{ setting('geo_placename') }}">
    @endif
    @if (filled(setting('geo_latitude')) && filled(setting('geo_longitude')))
        <meta name="geo.position" content="{{ setting('geo_latitude') }};{{ setting('geo_longitude') }}">
        <meta name="ICBM" content="{{ setting('geo_latitude') }}, {{ setting('geo_longitude') }}">
    @endif
    @if (filled(setting('google_site_verification')))
        <meta name="google-site-verification" content="{{ setting('google_site_verification') }}">
    @endif
    @if (filled(setting('bing_site_verification')))
        <meta name="msvalidate.01" content="{{ setting('bing_site_verification') }}">
    @endif

    <meta name="theme-color" content="#13293a">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="alternate" type="text/plain" href="{{ route('llms') }}" title="Site summary for AI assistants">

    {{-- Only the two fonts needed for the first screen are preloaded; others load when used. --}}
    <link rel="preload" as="font" type="font/woff2" href="{{ Vite::asset('resources/fonts/poppins-400.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ Vite::asset('resources/fonts/outfit-600.woff2') }}" crossorigin>
    {{-- Turns on the page animations before anything is drawn (none when the device asks for reduced motion). --}}
    <script>if (!matchMedia('(prefers-reduced-motion: reduce)').matches) document.documentElement.classList.add('reveal-on');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')

    <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::organization()) !!}</script>
    @stack('schema')

    @if (filled(setting('google_analytics_id')))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('google_analytics_id') }}"></script>
        <script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', @js(setting('google_analytics_id')));</script>
    @endif
</head>
<body class="flex min-h-screen flex-col">
    <div id="page-progress" class="page-progress" aria-hidden="true"></div>
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg">Skip to content</a>

    @include('partials.header')

    <main id="main" class="flex-1">
        @include('partials.flash')
        {{ $slot }}
    </main>

    @include('partials.footer')
    @include('partials.floating')

    @stack('scripts')
</body>
</html>
