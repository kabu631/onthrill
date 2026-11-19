@php
    $d = $destination;
    $countryName = \Illuminate\Support\Str::before($d->country, ' (');
    $crumbs = [
        wording('destinations_page_title') => route('destinations.index'),
        $countryName => route('destinations.country', \App\Support\Menu::countrySlug($d->country)),
        $d->name => null,
    ];
    $photos = collect([$d->image, ...($d->gallery ?? [])])->filter()->unique()->values()
        ->map(fn ($path, $i) => ['path' => $path, 'alt' => $d->name, 'credit' => $i === 0 ? $d->image_credit : null, 'credit_url' => $i === 0 ? $d->image_credit_url : null])
        ->all();
    $mapUrl = $d->latitude && $d->longitude
        ? 'https://www.google.com/maps/search/?api=1&query='.$d->latitude.','.$d->longitude
        : 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($d->name.', '.$d->region);
    $faqs = array_values(array_filter((array) $d->faqs, fn ($f) => filled($f['question'] ?? null)));
    $facts = array_filter([
        ['icon' => 'mountain', 'label' => wording('dest_fact_altitude'), 'value' => $d->altitudeLabel()],
        ['icon' => 'sun', 'label' => wording('dest_fact_best_time'), 'value' => $d->best_time],
        ['icon' => 'clock', 'label' => wording('dest_fact_hours'), 'value' => $d->opening_hours],
        ['icon' => 'map', 'label' => wording('dest_fact_airport'), 'value' => $d->nearest_airport],
    ], fn ($fact) => filled($fact['value']));
@endphp

<x-layout :title="$d->meta_title ?: $d->name.', '.$countryName" :description="$d->meta_description ?: $d->summary" :image="media_url($d->image)" type="article">
    <x-page-banner tall :title="$d->name" :crumbs="$crumbs" :intro="$d->summary" :image="media_url($d->image)" :credit="$d->image_credit" :credit-url="$d->image_credit_url">
        <p style="--d: 60ms" class="anim-rise mt-4 flex flex-wrap items-center justify-center gap-x-5 gap-y-1 text-sm text-white/85">
            <span class="flex items-center gap-1.5"><x-op-icon name="map-pin" class="size-4 text-saffron-400" /> {{ trim(implode(', ', array_filter([$d->region, $countryName])), ', ') }}</span>
        </p>
    </x-page-banner>

    @push('schema')
        <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::destination($d)) !!}</script>
        @if ($faqs)
            <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::faq($faqs)) !!}</script>
        @endif
    @endpush

    {{-- Key facts, overlapping the bottom of the photo --}}
    @if ($facts)
        <div class="relative z-10 -mt-12">
            <div class="container-page">
                <dl data-reveal class="grid grid-cols-2 gap-px overflow-hidden rounded-lg bg-navy-100 shadow-lift {{ [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3'][count($facts)] ?? 'lg:grid-cols-4' }}">
                    @foreach ($facts as $fact)
                        <div class="flex items-start gap-3 bg-white p-5">
                            <x-op-icon :name="$fact['icon']" class="mt-0.5 size-6 text-saffron-600" />
                            <div class="min-w-0">
                                <dt class="text-xs text-navy-500">{{ $fact['label'] }}</dt>
                                <dd class="mt-1 text-sm leading-snug font-semibold text-navy-900">{{ $fact['value'] }}</dd>
                            </div>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    @endif

    <div class="container-page section pt-12 sm:pt-16">
        <div class="grid gap-12 lg:grid-cols-[1fr_22rem]">
            <article class="min-w-0">
                <div data-reveal>
                    <h2 class="text-2xl font-bold sm:text-3xl">{{ wording('dest_about_title', ['name' => $d->name]) }}</h2>
                    <span class="title-rule"></span>
                    <div class="rich mt-6">{!! clean_html($d->description) !!}</div>
                </div>

                @if (count($photos) > 1)
                    <div data-reveal>
                        <h2 class="mt-12 text-2xl font-bold">{{ wording('dest_photos_title') }}</h2>
                        <div class="mt-5">
                            @include('partials.photo-grid', ['photos' => $photos])
                        </div>
                    </div>
                @endif

                @if (filled($d->how_to_reach))
                    <div data-reveal>
                        <h2 class="mt-12 text-2xl font-bold">{{ wording('dest_reach_title', ['name' => $d->name]) }}</h2>
                        <div class="mt-4 flex gap-4 rounded-lg border border-navy-100 bg-mist p-5">
                            <x-op-icon name="map-pin" class="mt-0.5 size-6 text-saffron-600" />
                            <div>
                                <p class="leading-relaxed">{{ $d->how_to_reach }}</p>
                                <a href="{{ $mapUrl }}" target="_blank" rel="noopener" class="more-link mt-3">{{ wording('dest_map_link') }} <x-op-icon name="arrow-right" class="size-4" /></a>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($faqs)
                    <div data-reveal>
                        <h2 class="mt-12 text-2xl font-bold">{{ wording('dest_faq_title', ['name' => $d->name]) }}</h2>
                        <div class="mt-5 space-y-3">
                            @foreach ($faqs as $faq)
                                <details class="group rounded-lg border border-navy-100 bg-white open:shadow-card" @if ($loop->first) open @endif>
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-display text-[17px] font-semibold text-navy-900 [&::-webkit-details-marker]:hidden">
                                        {{ $faq['question'] }}
                                        <span class="grid size-7 shrink-0 place-items-center rounded-md bg-mist text-saffron-600 group-open:bg-saffron-600 group-open:text-white"><x-op-icon name="plus" class="size-4 group-open:rotate-45" /></span>
                                    </summary>
                                    <p class="px-5 pb-5 leading-relaxed">{{ $faq['answer'] ?? '' }}</p>
                                </details>
                            @endforeach
                        </div>
                    </div>
                @endif

                <p class="mt-10 text-xs text-ink/55">{{ wording('last_updated', ['date' => $d->updated_at?->format('d F Y')]) }}</p>
            </article>

            {{-- Enquiry about this place, kept in view while reading --}}
            <aside>
                <div class="space-y-5 lg:sticky lg:top-28">
                    <div id="enquiry" data-reveal class="card scroll-mt-28 overflow-hidden">
                        <div class="bg-navy-900 px-6 py-5 text-white">
                            <h2 class="font-display text-xl font-semibold text-white">{{ wording('dest_enquiry_title', ['name' => $d->name]) }}</h2>
                            <p class="mt-1 text-sm text-white/70">{{ wording('dest_enquiry_text') }}</p>
                        </div>
                        <div class="px-6 pb-6">
                            @include('partials.inquiry-form', ['destination' => $d, 'compact' => true])
                        </div>
                    </div>

                    @if ($packages->isNotEmpty())
                        <a href="#tours" class="btn btn-dark w-full">{{ wording('dest_tours_button', ['count' => $packages->count(), 'name' => $d->name]) }}</a>
                    @endif
                </div>
            </aside>
        </div>
    </div>

    @if ($packages->isNotEmpty())
        <section id="tours" class="section scroll-mt-24 bg-mist">
            <div class="container-page">
                <x-section-heading align="left" :title="wording('dest_tours_title', ['name' => $d->name])" />
                <div data-reveal-group class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($packages as $package)
                        <x-package-card :package="$package" :currency="visitor_currency()" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($nearby->isNotEmpty())
        <section class="section">
            <div class="container-page">
                <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
                    <x-section-heading align="left" :title="wording('dest_nearby_title', ['country' => $countryName])" />
                    <a href="{{ route('destinations.country', \App\Support\Menu::countrySlug($d->country)) }}" class="more-link mb-1">{{ wording('dest_nearby_link', ['country' => $countryName]) }} <x-op-icon name="arrow-right" class="size-4" /></a>
                </div>
                <div data-reveal-group class="mt-10 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                    @foreach ($nearby as $other)
                        <x-destination-card :destination="$other" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layout>
