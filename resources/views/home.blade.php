{{--
    Each section is its own partial in resources/views/home. Which ones are
    shown is chosen in Admin > Website > Page Content > Homepage.
--}}
@php
    $partials = [
        'hero' => 'home.hero',
        'facts' => 'home.facts',
        'destinations' => 'home.destinations',
        'packages' => 'home.packages',
        'why' => 'home.why',
        'how' => 'home.how-it-works',
        'testimonials' => 'home.testimonials',
        'gallery' => 'home.gallery',
        'faq' => 'home.faq',
        'cta' => 'home.cta',
    ];
    $shown = (array) setting('home_sections', array_keys($partials));

    // Sections that have nothing to show are skipped by their partial too.
    $hasContent = [
        'destinations' => $featuredDestinations->isNotEmpty(),
        'packages' => $packages->isNotEmpty(),
        'why' => filled((array) setting('why_items')),
        'how' => filled((array) setting('how_steps')),
        'testimonials' => $testimonials->isNotEmpty(),
        'gallery' => $gallery->isNotEmpty(),
        'faq' => filled((array) setting('faqs')),
    ];
    $visible = array_values(array_filter(array_keys($partials), fn ($key) => in_array($key, $shown, true) && ($hasContent[$key] ?? true)));

    // The hero, the facts strip under it and the call-to-action band have their own
    // backgrounds. The other sections alternate white and light grey-blue.
    $ownBackground = ['hero', 'facts', 'cta'];
    $bands = [];
    $count = 0;
    foreach ($visible as $key) {
        if (! in_array($key, $ownBackground, true)) {
            $bands[$key] = ['class' => $count++ % 2 === 0 ? 'bg-white' : 'bg-mist'];
        }
    }
@endphp

<x-layout :over-hero="in_array('hero', $visible, true)">
    @push('schema')
        {{-- The site and its search box, for search engines. --}}
        <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::website()) !!}</script>
    @endpush

    @foreach ($visible as $key)
        @include($partials[$key], ['band' => $bands[$key] ?? null])
    @endforeach
</x-layout>
