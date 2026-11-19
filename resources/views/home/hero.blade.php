@php
    // Slides from Admin > Page Content > Homepage. Without slides: one photo and rotating words.
    $slides = collect((array) setting('hero_slides'))->filter(fn ($s) => filled($s['image'] ?? null))->values();
    if ($slides->isEmpty() && filled(setting('hero_background'))) {
        $slides = collect([['image' => setting('hero_background')]]);
    }
    $slideTitles = $slides->isNotEmpty() && $slides->every(fn ($s) => filled($s['title'] ?? null));
    $words = $slideTitles ? [] : array_values(array_filter((array) setting('hero_words')));
    $lines = $slideTitles ? $slides->pluck('title')->all() : $words;

    // Each photo in the size the screen needs: 800 px on phones, up to the full 1920 px on large screens.
    $sources = $slides->map(function ($slide) {
        $path = $slide['image'];
        $set = collect([800, 1200])->map(fn ($w) => \App\Support\Thumbnails::url($path, $w).' '.$w.'w')->push(media_url($path).' 1920w');

        return ['src' => media_url($path), 'srcset' => $set->unique()->implode(', ')];
    });

    $byCountry = $destinations->groupBy('country');
    $tourTypes = \App\Support\Menu::get()['tour_types'];
    $currencyName = fn (string $code) => wording($code === 'NPR' ? 'currency_npr_name' : 'currency_inr_name');
@endphp

@if ($sources->isNotEmpty())
    @push('head')
        {{-- The first photo is the largest thing on the first screen: fetch it first. --}}
        <link rel="preload" as="image" imagesrcset="{{ $sources[0]['srcset'] }}" imagesizes="100vw" fetchpriority="high">
    @endpush
@endif

{{-- Hero: temple photographs one after another under the transparent menu bar, with the tour search across the bottom edge. --}}
<section x-data="heroSlider({{ max(1, $slides->count()) }})" class="relative isolate -mt-16 flex min-h-[38rem] items-center overflow-hidden bg-navy-950 pt-28 pb-36 text-white sm:min-h-[42rem] lg:-mt-20 lg:min-h-[46rem] lg:pt-32 lg:pb-40">
    @foreach ($slides as $index => $slide)
        <div class="absolute inset-0 -z-20 transition-opacity duration-1000 ease-in-out"
             @if ($index > 0) style="opacity: 0" @endif
             :style="index === {{ $index }} ? 'opacity: 1' : 'opacity: 0'"
             :aria-hidden="index !== {{ $index }}">
            <img src="{{ $sources[$index]['src'] }}" srcset="{{ $sources[$index]['srcset'] }}" sizes="100vw"
                 alt="{{ $slide['place'] ?? setting('site_name') }}"
                 @if ($index === 0) fetchpriority="high" @else loading="lazy" decoding="async" @endif
                 class="size-full object-cover object-center">
        </div>
    @endforeach
    {{-- Shade so the white text reads on any photo --}}
    <div class="absolute inset-0 -z-10 bg-navy-950/40" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 bg-linear-to-b from-navy-950/65 via-transparent to-navy-950/75" aria-hidden="true"></div>
    <div class="absolute inset-0 -z-10 hidden bg-linear-to-r from-navy-950/55 via-navy-950/15 to-transparent lg:block" aria-hidden="true"></div>

    <div class="container-page">
        <div class="max-w-3xl">
            <h1 class="anim-rise text-[2.6rem] leading-[1.05] font-bold text-white sm:text-6xl lg:text-7xl" @if ($words) x-data="rotator(@js($words))" @endif>
                <span class="block">{{ setting('hero_eyebrow') }}</span>
                {{-- All lines share one grid cell, so the tallest reserves the height and nothing jumps. --}}
                @if ($lines)
                    <span class="grid text-saffron-400">
                        @foreach ($lines as $i => $line)
                            <span class="col-start-1 row-start-1 transition-opacity duration-1000"
                                  @if ($i > 0) x-cloak @endif
                                  :class="index === {{ $i }} ? 'opacity-100' : 'opacity-0'"
                                  :aria-hidden="index !== {{ $i }}">{{ $line }}</span>
                        @endforeach
                    </span>
                @endif
            </h1>

            <p style="--d: 120ms" class="anim-rise mt-6 max-w-xl text-base leading-relaxed text-white/90 sm:text-lg">{{ setting('hero_text') }}</p>

            <div style="--d: 240ms" class="anim-rise mt-9 flex flex-wrap items-center gap-3">
                @if (filled(setting('hero_primary_label')))
                    <a href="{{ url(setting('hero_primary_url')) }}" class="btn btn-primary btn-lg">{{ setting('hero_primary_label') }}</a>
                @endif
                @if (filled(setting('hero_secondary_label')))
                    <a href="{{ url(setting('hero_secondary_url')) }}" class="btn btn-light btn-lg">{{ setting('hero_secondary_label') }}</a>
                @endif
            </div>
        </div>
    </div>

    {{-- Where the photo was taken, its credit, and the slide controls --}}
    @if ($slides->count() > 0)
        <div class="absolute inset-x-0 bottom-24 lg:bottom-28">
            <div class="container-page flex items-end justify-between gap-6">
                <div class="grid min-w-0 text-sm">
                    @foreach ($slides as $index => $slide)
                        <p class="col-start-1 row-start-1 min-w-0 transition-opacity duration-1000" @if ($index > 0) x-cloak @endif :class="index === {{ $index }} ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                            @if (filled($slide['place'] ?? null))
                                @if (filled($slide['link'] ?? null))
                                    <a href="{{ url($slide['link']) }}" class="flex items-center gap-1.5 font-medium text-white hover:text-saffron-300"><x-op-icon name="map-pin" class="size-4 text-saffron-400" /> {{ $slide['place'] }}</a>
                                @else
                                    <span class="flex items-center gap-1.5 font-medium text-white"><x-op-icon name="map-pin" class="size-4 text-saffron-400" /> {{ $slide['place'] }}</span>
                                @endif
                            @endif
                            @if (filled($slide['credit'] ?? null))
                                <span class="mt-0.5 block truncate pl-5.5 text-[11px] text-white/55">{{ $slide['credit'] }}</span>
                            @endif
                        </p>
                    @endforeach
                </div>

                @if ($slides->count() > 1)
                    <div class="hidden shrink-0 items-center gap-3 sm:flex">
                        <button type="button" class="grid size-10 place-items-center rounded-md border border-white/40 text-white transition-colors hover:border-white hover:bg-white hover:text-navy-900" @click="go(index - 1)" aria-label="Previous photo">
                            <x-op-icon name="arrow-left" class="size-4" />
                        </button>
                        <span class="w-14 text-center font-display text-sm font-semibold tabular-nums">
                            <span x-text="String(index + 1).padStart(2, '0')">01</span><span class="text-white/50"> / {{ str_pad((string) $slides->count(), 2, '0', STR_PAD_LEFT) }}</span>
                        </span>
                        <button type="button" class="grid size-10 place-items-center rounded-md border border-white/40 text-white transition-colors hover:border-white hover:bg-white hover:text-navy-900" @click="go(index + 1)" aria-label="Next photo">
                            <x-op-icon name="arrow-right" class="size-4" />
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif
</section>

{{-- Tour search: overlaps the bottom of the photo and sends the visitor to the matching packages --}}
<div class="relative z-10 -mt-16 lg:-mt-14">
    <div class="container-page">
        <form action="{{ route('packages.index') }}" method="get" style="--d: 300ms" class="anim-rise grid overflow-hidden rounded-lg bg-white text-ink shadow-lift sm:grid-cols-2 lg:grid-cols-[1.3fr_1.2fr_1fr_auto]" aria-label="{{ wording('search_button') }}">
            <label class="block border-b border-navy-100 px-5 py-4 sm:border-r lg:border-b-0">
                <span class="flex items-center gap-2 text-xs font-semibold text-navy-500"><x-op-icon name="map-pin" class="size-4 text-saffron-600" /> {{ wording('search_destination') }}</span>
                <select name="destination" class="mt-1 w-full cursor-pointer appearance-none bg-transparent text-[15px] font-medium text-navy-900 focus:outline-none">
                    <option value="">{{ wording('search_anywhere') }}</option>
                    @foreach ($byCountry as $country => $list)
                        <optgroup label="{{ $country }}">
                            @foreach ($list as $destination)
                                <option value="{{ $destination->slug }}">{{ $destination->name }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </label>
            <label class="block border-b border-navy-100 px-5 py-4 lg:border-r lg:border-b-0">
                <span class="flex items-center gap-2 text-xs font-semibold text-navy-500"><x-op-icon name="map" class="size-4 text-saffron-600" /> {{ wording('search_type') }}</span>
                <select name="type" class="mt-1 w-full cursor-pointer appearance-none bg-transparent text-[15px] font-medium text-navy-900 focus:outline-none">
                    <option value="">{{ wording('search_all_types') }}</option>
                    @foreach ($tourTypes as $type)
                        <option value="{{ \Illuminate\Support\Str::afterLast($type['url'], '/') }}">{{ $type['name'] }}</option>
                    @endforeach
                </select>
            </label>
            <label class="block border-b border-navy-100 px-5 py-4 sm:border-r sm:border-b-0">
                <span class="flex items-center gap-2 text-xs font-semibold text-navy-500"><x-op-icon name="calendar" class="size-4 text-saffron-600" /> {{ wording('search_duration') }}</span>
                <select name="duration" class="mt-1 w-full cursor-pointer appearance-none bg-transparent text-[15px] font-medium text-navy-900 focus:outline-none">
                    <option value="">{{ wording('search_any_length') }}</option>
                    @foreach (array_keys(\App\Http\Controllers\PackageController::DURATIONS) as $value)
                        <option value="{{ $value }}">{{ wording('duration_'.$value) }}</option>
                    @endforeach
                </select>
            </label>
            <div class="p-3">
                <button type="submit" class="btn btn-primary h-full min-h-12 w-full px-8 text-[15px]"><x-op-icon name="search" class="size-5" /> {{ wording('search_button') }}</button>
            </div>
        </form>
        <p class="mt-3 text-center text-xs text-ink/55">
            {{ wording('search_price_note', ['currency' => $currencyName($currency)]) }}
            <a href="{{ route('currency', $currency === 'NPR' ? 'inr' : 'npr') }}" rel="nofollow" class="font-medium text-saffron-700 underline underline-offset-2 hover:text-saffron-800">{{ wording('search_price_switch', ['currency' => $currencyName($currency === 'NPR' ? 'INR' : 'NPR')]) }}</a>
        </p>
    </div>
</div>
