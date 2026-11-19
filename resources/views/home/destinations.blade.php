@if ($featuredDestinations->isNotEmpty())
    @php
        $countries = $destinations->pluck('country')->unique()->map(fn ($c) => \Illuminate\Support\Str::before($c, ' ('))->values();
    @endphp
    <section class="section {{ $band['class'] ?? '' }}">
        <div class="container-page">
            <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
                <x-section-heading align="left" :title="setting('destinations_title')" :text="setting('destinations_text')" />
                <a href="{{ route('destinations.index') }}" class="more-link mb-1">{{ wording('home_destinations_link') }} <x-op-icon name="arrow-right" class="size-4" /></a>
            </div>

            <div data-reveal-group class="mt-10 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                @foreach ($featuredDestinations as $destination)
                    <x-destination-card :destination="$destination" />
                @endforeach

                {{-- Last tile: the way to every other place --}}
                <a href="{{ route('destinations.index') }}" class="group flex aspect-[4/5] flex-col justify-between rounded-lg bg-navy-900 p-5 text-white transition-colors hover:bg-navy-800 sm:p-6">
                    <x-op-icon name="map" class="size-9 text-saffron-400" />
                    <span>
                        <span class="block font-display text-5xl leading-none font-bold">{{ $destinations->count() }}</span>
                        <span class="mt-2 block text-sm text-white/75">{{ wording('home_destinations_tile', ['countries' => $countries->implode(', ')]) }}</span>
                        <span class="mt-5 flex items-center gap-1.5 text-sm font-semibold text-saffron-300">
                            {{ wording('home_destinations_tile_link') }} <x-op-icon name="arrow-right" class="size-4" />
                        </span>
                    </span>
                </a>
            </div>
        </div>
    </section>
@endif
