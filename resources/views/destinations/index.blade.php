@php
    $allTitle = wording('destinations_page_title');
    $countryName = $country ? \Illuminate\Support\Str::before($country, ' (') : null;
    $title = $countryName ? wording('country_page_title', ['country' => $countryName]) : $allTitle;
    $crumbs = $country ? [$allTitle => route('destinations.index'), $countryName => null] : [$allTitle => null];
    // Country intros: Admin > Page Content > Destinations > Country pages
    $intro = $country ? wording('country_intro_'.\App\Support\Menu::countrySlug($country)) : wording('destinations_page_intro');
    $places = fn (int $count) => wording($count === 1 ? 'menu_places_one' : 'menu_places_many', ['count' => $count]);
    $all = collect($groups)->flatten(1);
    $tab = 'rounded-md border px-5 py-2.5 text-sm font-medium whitespace-nowrap transition-colors';
@endphp

<x-layout :title="$title" :description="$intro">
    <x-page-banner :title="$title" :crumbs="$crumbs" :intro="$intro" />

    @push('schema')
        <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::itemList($title, $all)) !!}</script>
    @endpush

    <div class="container-page section pt-10 sm:pt-14">
        <nav aria-label="Countries" class="no-scrollbar -mx-4 flex justify-start gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:justify-center sm:px-0">
            <a href="{{ route('destinations.index') }}" @class([$tab, 'border-navy-900 bg-navy-900 text-white' => ! $country, 'border-navy-200 bg-white text-navy-800 hover:border-navy-900' => $country])>{{ wording('dest_tab_all') }}</a>
            @foreach (\App\Support\Menu::COUNTRY_SLUGS as $slug => $name)
                <a href="{{ route('destinations.country', $slug) }}" @class([$tab, 'border-navy-900 bg-navy-900 text-white' => $country === $name, 'border-navy-200 bg-white text-navy-800 hover:border-navy-900' => $country !== $name])>{{ \Illuminate\Support\Str::before($name, ' (') }}</a>
            @endforeach
        </nav>

        @forelse ($groups as $groupCountry => $list)
            <section class="mt-12 first-of-type:mt-10">
                @unless ($country)
                    <div class="flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 class="text-2xl font-bold sm:text-3xl">{{ \Illuminate\Support\Str::before($groupCountry, ' (') }}</h2>
                            <span class="title-rule"></span>
                        </div>
                        <a href="{{ route('destinations.country', \App\Support\Menu::countrySlug($groupCountry)) }}" class="more-link">{{ $places($list->count()) }} <x-op-icon name="arrow-right" class="size-4" /></a>
                    </div>
                @endunless
                <div data-reveal-group class="mt-7 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                    @foreach ($list as $destination)
                        <x-destination-card :destination="$destination" />
                    @endforeach
                </div>
            </section>
        @empty
            <p class="card mt-8 p-10 text-center">{{ wording('dest_empty') }}</p>
        @endforelse
    </div>
</x-layout>
