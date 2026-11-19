<x-layout :title="$term ? wording('search_title').': '.$term : wording('search_title')" noindex>
    <x-page-banner :title="wording('search_title')" :crumbs="[wording('search_title') => null]" />

    <div class="container-page section pt-8 sm:pt-10">
        <form action="{{ route('search') }}" method="get" role="search" class="relative mx-auto max-w-2xl">
            <label for="search-page" class="sr-only">{{ wording('search_title') }}</label>
            <input id="search-page" type="search" name="q" value="{{ $term }}" placeholder="{{ wording('search_placeholder') }}" autofocus class="input py-4 pr-32 pl-12 text-base">
            <x-op-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-ink/40" />
            <button type="submit" class="btn btn-primary btn-sm absolute top-1/2 right-2 -translate-y-1/2">{{ wording('search_page_button') }}</button>
        </form>

        @if (mb_strlen($term) >= 2)
            @if ($packages->isEmpty() && $destinations->isEmpty())
                <div data-reveal class="card mx-auto mt-10 max-w-lg p-8 text-center">
                    <p class="font-display text-xl font-bold text-navy-900">{{ wording('search_none_title', ['term' => $term]) }}</p>
                    <p class="mt-2 text-sm">{{ wording('search_none_text') }}</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary mt-5">{{ wording('list_empty_button') }}</a>
                </div>
            @endif

            @if ($packages->isNotEmpty())
                <section class="mt-12">
                    <h2 class="text-2xl font-bold">{{ wording('search_packages') }} <span class="font-sans text-base font-normal text-ink/50">({{ $packages->count() }})</span></h2>
                    <div data-reveal-group class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($packages as $package)
                            <x-package-card :package="$package" :currency="visitor_currency()" />
                        @endforeach
                    </div>
                </section>
            @endif

            @if ($destinations->isNotEmpty())
                <section class="mt-12">
                    <h2 class="text-2xl font-bold">{{ wording('search_destinations') }} <span class="font-sans text-base font-normal text-ink/50">({{ $destinations->count() }})</span></h2>
                    <div data-reveal-group class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                        @foreach ($destinations as $destination)
                            <x-destination-card :destination="$destination" />
                        @endforeach
                    </div>
                </section>
            @endif
        @elseif ($term !== '')
            <p class="mt-8 text-center text-sm">{{ wording('search_too_short') }}</p>
        @endif
    </div>
</x-layout>
