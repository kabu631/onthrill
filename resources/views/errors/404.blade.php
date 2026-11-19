<x-layout :title="wording('e404_title')" noindex>
    <div data-reveal class="container-page section text-center">
        <p class="font-display text-7xl font-bold text-saffron-500">404</p>
        <h1 class="mt-4 text-3xl font-bold sm:text-4xl">{{ wording('e404_title') }}</h1>
        <p class="mx-auto mt-3 max-w-md">{{ wording('e404_text') }}</p>

        <form action="{{ route('search') }}" method="get" role="search" class="relative mx-auto mt-8 max-w-md">
            <label for="search-404" class="sr-only">{{ wording('search_title') }}</label>
            <input id="search-404" type="search" name="q" placeholder="{{ wording('search_placeholder') }}" class="input py-3.5 pl-12">
            <x-op-icon name="search" class="pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-ink/40" />
        </form>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="{{ route('home') }}" class="btn btn-primary">{{ wording('e404_home') }}</a>
            <a href="{{ route('destinations.index') }}" class="btn btn-outline">{{ wording('menu_destinations') }}</a>
            <a href="{{ route('packages.index') }}" class="btn btn-outline">{{ wording('menu_packages') }}</a>
        </div>
    </div>
</x-layout>
