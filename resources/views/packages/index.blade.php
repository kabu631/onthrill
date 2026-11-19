@php
    $allTitle = wording('packages_title');
    $title = $category?->name ?? ($destination ? wording('list_destination_title', ['name' => $destination->name]) : $allTitle);
    $intro = $category?->description ?: ($destination ? wording('list_destination_intro', ['name' => $destination->name]) : wording('packages_intro'));
    $crumbs = [$allTitle => ($category || $destination) ? route('packages.index') : null];
    if ($category) {
        $crumbs[$category->name] = null;
    } elseif ($destination) {
        $crumbs[$destination->name] = null;
    }
    // Filters other than the tour type, kept when switching tour type.
    $keep = array_filter(['destination' => $destination?->slug, 'duration' => $duration, 'sort' => $sort !== 'recommended' ? $sort : null]);
    $withKeep = fn (string $url) => $url.($keep ? '?'.http_build_query($keep) : '');
    $filtered = $destination || $duration;
    $here = $category ? $category->url() : route('packages.index');
    $total = $packages->total();
@endphp

<x-layout :title="$category?->meta_title ?: $title" :description="$category?->meta_description ?: $intro" :noindex="$filtered">
    <x-page-banner :title="$title" :crumbs="$crumbs" :intro="$intro" />

    @push('schema')
        <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::itemList($title, $packages)) !!}</script>
    @endpush

    <div class="container-page section pt-10 sm:pt-14" x-data="tourList">
        <div class="grid gap-8 lg:grid-cols-[17rem_1fr] lg:gap-10">
            {{-- Filters: a panel that opens on phones, a sidebar on large screens --}}
            <aside aria-label="{{ wording('list_filter_button') }}" class="min-w-0">
                <button type="button" class="btn btn-outline w-full justify-between lg:hidden" @click="filters = !filters" :aria-expanded="filters">
                    <span class="flex items-center gap-2"><x-op-icon name="filters" class="size-4" /> {{ wording('list_filter_button') }}</span>
                    <x-op-icon name="chevron-down" class="size-4" ::class="filters && 'rotate-180'" />
                </button>

                <div class="mt-3 hidden space-y-5 lg:sticky lg:top-28 lg:mt-0 lg:block" :class="filters && '!block'">
                    <div data-reveal class="card p-5">
                        <h2 class="text-lg font-semibold">{{ wording('list_type_title') }}</h2>
                        <ul class="mt-3 space-y-0.5 text-sm">
                            <li>
                                <a href="{{ $withKeep(route('packages.index')) }}" @class(['flex items-center justify-between rounded-md px-3 py-2', 'bg-saffron-50 font-semibold text-saffron-700' => ! $category, 'hover:bg-mist' => $category])>
                                    {{ wording('list_all_types') }} <span class="text-xs font-normal text-ink/45">{{ $categories->sum('packages_count') }}</span>
                                </a>
                            </li>
                            @foreach ($categories as $item)
                                <li>
                                    <a href="{{ $withKeep($item->url()) }}" @class(['flex items-center justify-between gap-3 rounded-md px-3 py-2', 'bg-saffron-50 font-semibold text-saffron-700' => $category?->id === $item->id, 'hover:bg-mist' => $category?->id !== $item->id])>
                                        {{ $item->name }} <span class="text-xs font-normal text-ink/45">{{ $item->packages_count }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <form data-reveal method="get" action="{{ $here }}" class="card space-y-6 p-5">
                        @if ($sort !== 'recommended')
                            <input type="hidden" name="sort" value="{{ $sort }}">
                        @endif
                        <div>
                            <label for="filter-destination" class="text-lg font-semibold text-navy-900">{{ wording('list_destination_heading') }}</label>
                            <select id="filter-destination" name="destination" class="input mt-3" onchange="this.form.submit()">
                                <option value="">{{ wording('list_anywhere') }}</option>
                                @foreach ($destinationOptions as $country => $list)
                                    <optgroup label="{{ $country }}">
                                        @foreach ($list as $option)
                                            <option value="{{ $option->slug }}" @selected($destination?->slug === $option->slug)>{{ $option->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        <fieldset>
                            <legend class="text-lg font-semibold text-navy-900">{{ wording('list_length_title') }}</legend>
                            <div class="mt-3 space-y-2 text-sm">
                                @foreach (['' => wording('list_any_length')] + collect($durations)->mapWithKeys(fn ($d, $key) => [$key => wording('duration_'.$key)])->all() as $value => $label)
                                    <label class="flex cursor-pointer items-center gap-3 rounded-md px-1 py-1 hover:text-saffron-700">
                                        <input type="radio" name="duration" value="{{ $value }}" @checked((string) $duration === (string) $value) class="size-4 accent-saffron-600" onchange="this.form.submit()">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        <noscript><button type="submit" class="btn btn-primary w-full">{{ wording('list_filter_button') }}</button></noscript>
                        @if ($filtered)
                            <a href="{{ $here }}" class="block text-center text-sm font-medium text-saffron-700 underline underline-offset-2">{{ wording('list_clear') }}</a>
                        @endif
                    </form>

                    {{-- Help for anyone who has not found the right tour --}}
                    <div data-reveal class="rounded-lg bg-navy-900 p-5 text-sm text-white/80">
                        <p class="font-display text-lg font-semibold text-white">{{ wording('list_help_title') }}</p>
                        <p class="mt-1.5">{{ wording('list_help_text') }}</p>
                        <a href="{{ route('contact') }}" class="btn btn-primary btn-sm mt-4">{{ wording('list_help_button') }}</a>
                    </div>
                </div>
            </aside>

            <div class="min-w-0">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-navy-100 pb-4">
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <span>{!! wording_html($total === 1 ? 'list_found_one' : 'list_found_many', ['count' => $total]) !!}</span>
                        @if ($destination)
                            <a href="{{ $here.'?'.http_build_query(array_filter(['duration' => $duration, 'sort' => $keep['sort'] ?? null])) }}" class="inline-flex items-center gap-1 rounded-md bg-mist px-2 py-1 text-xs font-medium text-navy-800 hover:bg-navy-100">
                                {{ $destination->name }} <x-op-icon name="x" class="size-3.5" /><span class="sr-only">{{ wording('list_clear') }}</span>
                            </a>
                        @endif
                        @if ($duration)
                            <a href="{{ $here.'?'.http_build_query(array_filter(['destination' => $destination?->slug, 'sort' => $keep['sort'] ?? null])) }}" class="inline-flex items-center gap-1 rounded-md bg-mist px-2 py-1 text-xs font-medium text-navy-800 hover:bg-navy-100">
                                {{ wording('duration_'.$duration) }} <x-op-icon name="x" class="size-3.5" /><span class="sr-only">{{ wording('list_clear') }}</span>
                            </a>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                    {{-- List or grid; the choice is remembered in this browser --}}
                    <div class="hidden items-center gap-1 sm:flex" role="group" aria-label="{{ wording('list_view_label') }}">
                        <button type="button" class="grid size-10 place-items-center rounded-md border transition-colors" :class="view === 'list' ? 'border-navy-900 bg-navy-900 text-white' : 'border-navy-200 text-navy-800 hover:border-navy-900'" @click="setView('list')" :aria-pressed="view === 'list'" title="{{ wording('list_view_list') }}"><x-op-icon name="view-list" class="size-5" /><span class="sr-only">{{ wording('list_view_list') }}</span></button>
                        <button type="button" class="grid size-10 place-items-center rounded-md border transition-colors" :class="view === 'grid' ? 'border-navy-900 bg-navy-900 text-white' : 'border-navy-200 text-navy-800 hover:border-navy-900'" @click="setView('grid')" :aria-pressed="view === 'grid'" title="{{ wording('list_view_grid') }}"><x-op-icon name="view-grid" class="size-5" /><span class="sr-only">{{ wording('list_view_grid') }}</span></button>
                    </div>

                    <form method="get" action="{{ $here }}" class="flex items-center gap-2">
                        @foreach (array_filter(['destination' => $destination?->slug, 'duration' => $duration]) as $name => $value)
                            <input type="hidden" name="{{ $name }}" value="{{ $value }}">
                        @endforeach
                        <label for="sort" class="text-sm whitespace-nowrap">{{ wording('list_sort') }}</label>
                        <select id="sort" name="sort" class="input w-auto py-2" onchange="this.form.submit()">
                            @foreach (array_keys($sorts) as $value)
                                <option value="{{ $value }}" @selected($sort === $value)>{{ wording('sort_'.$value) }}</option>
                            @endforeach
                        </select>
                        <noscript><button type="submit" class="btn btn-outline btn-sm">{{ wording('list_sort') }}</button></noscript>
                    </form>
                    </div>
                </div>

                @if ($packages->isEmpty())
                    <div data-reveal class="card mt-6 p-10 text-center">
                        <x-op-icon name="map" class="mx-auto size-10 text-navy-300" />
                        <p class="mt-4 font-display text-xl font-semibold text-navy-900">{{ wording('list_empty_title') }}</p>
                        <p class="mt-2 text-sm">{{ wording('list_empty_text') }}</p>
                        <div class="mt-6 flex flex-wrap justify-center gap-3">
                            @if ($filtered)
                                <a href="{{ $here }}" class="btn btn-outline">{{ wording('list_clear') }}</a>
                            @endif
                            <a href="{{ route('contact') }}" class="btn btn-primary">{{ wording('list_empty_button') }}</a>
                        </div>
                    </div>
                @else
                    <div data-reveal-group data-view="list" :data-view="view" class="mt-6 grid gap-6" :class="view === 'grid' && 'sm:grid-cols-2 lg:grid-cols-3'">
                        @foreach ($packages as $package)
                            <x-package-card :package="$package" :currency="$currency" />
                        @endforeach
                    </div>

                    {{ $packages->links() }}
                @endif
            </div>
        </div>
    </div>
</x-layout>
