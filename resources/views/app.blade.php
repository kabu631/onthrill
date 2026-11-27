{{--
    The page shell for every public page. The Angular app (frontend/, built into
    public/app) draws the page; this sends what search engines and link previews
    read first, an outline of the content for crawlers without JavaScript, and
    the data the app needs to draw the first screen at once.
    Filled by App\Http\Controllers\ShellController.
--}}
@php
    $siteName = setting('site_name');
    $pageTitle = $title ? $title.' | '.$siteName : setting('meta_title');
    $pageDescription = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) ($description ?: setting('meta_description'))))), 160);
    $shareImage = $image ?: media_url(setting('share_image'));
    // Canonical URL: the page without tracking or filter parameters (pagination is kept).
    $canonical = url()->current().(request()->integer('page') > 1 ? '?page='.request()->integer('page') : '');
    $boot = \App\Support\SiteData::payload();
    if ($api) {
        $boot['page'] = ['api' => $api, 'data' => $data];
    }
    $schemas = array_filter([\App\Support\Seo::organization(), ...$schemas]);
@endphp
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <base href="/">

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
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('app/fonts/poppins-400.woff2') }}" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="{{ asset('app/fonts/outfit-700.woff2') }}" crossorigin>

    @foreach (\App\Support\SpaAssets::tags() as $tag)
        {!! $tag !!}
    @endforeach

    @foreach ($schemas as $schema)
        <script type="application/ld+json">{!! \App\Support\Seo::json($schema) !!}</script>
    @endforeach

    {{-- Read by the app on start (frontend/src/app/core/site.ts). --}}
    <script>window.__ONTHRILL__ = {!! json_encode($boot, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_PARTIAL_OUTPUT_ON_ERROR) !!};</script>

    @if (filled(setting('google_analytics_id')))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ setting('google_analytics_id') }}"></script>
        <script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', @js(setting('google_analytics_id')));</script>
    @endif
</head>
<body>
    {{-- Replaced by the app as soon as it starts. --}}
    <app-root>
        <div class="shell-fallback">
            <header>
                <a href="{{ route('home') }}"><strong>{{ $siteName }}</strong></a>
                <nav aria-label="Main">
                    <a href="{{ route('destinations.index') }}">{{ wording('menu_destinations') }}</a>
                    <a href="{{ route('packages.index') }}">{{ wording('menu_packages') }}</a>
                    <a href="{{ route('gallery') }}">{{ wording('gallery_title') }}</a>
                    <a href="{{ route('contact') }}">{{ wording('menu_contact') }}</a>
                </nav>
            </header>
            <main>
                @if ($heading)
                    <h1>{{ $heading }}</h1>
                @endif
                @if ($intro)
                    <p>{{ $intro }}</p>
                @endif
                @if ($html)
                    <div>{!! $html !!}</div>
                @endif
                @if ($items)
                    <ul>
                        @foreach ($items as [$name, $link, $note])
                            <li>
                                @if ($link)<a href="{{ $link }}">{{ $name }}</a>@else<strong>{{ $name }}</strong>@endif
                                @if ($note) <span>{{ $note }}</span>@endif
                            </li>
                        @endforeach
                    </ul>
                @endif
                @unless (\App\Support\SpaAssets::built())
                    <p><em>The website app has not been built yet. Run <code>npm run build</code> in the project folder.</em></p>
                @endunless
            </main>
        </div>
    </app-root>
    <noscript><p style="text-align:center;padding:1rem">This website works best with JavaScript turned on.</p></noscript>
</body>
</html>
