@if ($packages->isNotEmpty())
    @php
        // Tabs for the tour types that have a package on the homepage.
        $types = $packages->pluck('category')->filter()->unique('id')->sortBy('sort_order')->values();
    @endphp
    <section class="section {{ $band['class'] ?? '' }}" x-data="{ tab: 'all' }">
        <div class="container-page">
            <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-6">
                <x-section-heading align="left" :title="setting('home_packages_title')" />

                @if ($types->count() > 1)
                    <div class="no-scrollbar -mx-4 flex gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:px-0" role="tablist" aria-label="Tour types">
                        <button type="button" role="tab" class="shrink-0 rounded-md border px-4 py-2 text-sm font-medium whitespace-nowrap transition-colors"
                                :class="tab === 'all' ? 'border-navy-900 bg-navy-900 text-white' : 'border-navy-200 bg-white text-navy-800 hover:border-navy-900'"
                                :aria-selected="tab === 'all'" @click="tab = 'all'">{{ wording('home_packages_all_tab') }}</button>
                        @foreach ($types as $type)
                            <button type="button" role="tab" class="shrink-0 rounded-md border border-navy-200 bg-white px-4 py-2 text-sm font-medium whitespace-nowrap text-navy-800 transition-colors hover:border-navy-900"
                                    :class="tab === '{{ $type->slug }}' && '!border-navy-900 !bg-navy-900 !text-white'"
                                    :aria-selected="tab === '{{ $type->slug }}'" @click="tab = '{{ $type->slug }}'">{{ $type->name }}</button>
                        @endforeach
                    </div>
                @endif
            </div>

            <div data-reveal-group class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($packages as $package)
                    <div x-show="tab === 'all' || tab === '{{ $package->category?->slug }}'">
                        <x-package-card :package="$package" :currency="$currency" />
                    </div>
                @endforeach
            </div>

            <div class="mt-12 text-center">
                <a href="{{ route('packages.index') }}" class="btn btn-dark btn-lg">{{ wording('home_packages_button', ['count' => $packageCount]) }}</a>
            </div>
        </div>
    </section>
@endif
