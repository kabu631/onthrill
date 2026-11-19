{{--
    Search pop-up, opened by the search icons in the header (or the "/" key).
    Results appear while typing (GET /search as JSON); Enter opens the full
    results page, which is also what happens without JavaScript.
--}}
<div x-data="searchPanel" x-cloak x-show="$store.search.open" class="fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Search" @keydown.escape.window="close()">
    <div x-show="$store.search.open" x-transition.opacity.duration.150ms class="absolute inset-0 bg-ink/70" @click="close()"></div>

    <div x-show="$store.search.open"
         x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="relative mx-auto w-full max-w-2xl px-3 pt-3 sm:px-4 sm:pt-20">
        <div class="overflow-hidden rounded-lg border-t-4 border-saffron-500 bg-white shadow-lift">
            <form action="{{ route('search') }}" method="get" role="search" class="flex items-center gap-3 border-b border-navy-100 px-4 sm:px-5">
                <x-op-icon name="search" class="size-5 shrink-0 text-saffron-600" />
                <label for="search-panel-input" class="sr-only">{{ wording('search_title') }}</label>
                <input id="search-panel-input" x-ref="input" x-model="term" @input.debounce.250ms="lookup()" type="search" name="q" autocomplete="off" maxlength="100"
                       placeholder="{{ wording('search_placeholder') }}"
                       class="h-14 min-w-0 flex-1 border-0 bg-transparent text-base text-ink placeholder:text-ink/40 focus:ring-0 focus:outline-none sm:h-16 sm:text-lg">
                <button type="button" @click="close()" class="shrink-0 rounded-md px-2 py-1 text-xs font-medium text-ink/55 hover:bg-navy-50 hover:text-ink">
                    <span class="hidden sm:inline">Esc</span><span class="sm:hidden">Close</span>
                </button>
            </form>

            <div class="max-h-[65vh] overflow-y-auto">
                {{-- Before typing: the categories as shortcuts --}}
                <div x-show="term.trim().length < 2" class="p-4 sm:p-5">
                    <p class="text-sm font-semibold text-navy-900">{{ wording('search_packages') }}</p>
                    <ul class="mt-2.5 flex flex-wrap gap-2 text-sm">
                        @foreach ($menu['tour_types'] as $item)
                            <li><a href="{{ $item['url'] }}" class="block rounded-md border border-navy-200 px-3 py-1.5 text-ink/80 hover:border-saffron-500 hover:text-saffron-700">{{ $item['name'] }}</a></li>
                        @endforeach
                    </ul>
                    @foreach ($menu['countries'] as $country)
                        <p class="mt-5 text-sm font-semibold text-navy-900">{{ $country['name'] }}</p>
                        <ul class="mt-2.5 flex flex-wrap gap-2 text-sm">
                            @foreach ($country['children'] as $item)
                                <li><a href="{{ $item['url'] }}" class="block rounded-md border border-navy-200 px-3 py-1.5 text-ink/80 hover:border-saffron-500 hover:text-saffron-700">{{ $item['name'] }}</a></li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>

                {{-- While typing --}}
                <div x-show="term.trim().length >= 2">
                    <p x-show="loading && ! results.length" class="px-5 py-6 text-sm text-ink/60">{{ wording('search_searching') }}</p>

                    <ul x-show="results.length" class="divide-y divide-navy-100">
                        <template x-for="result in results" :key="result.url">
                            <li>
                                <a :href="result.url" class="flex items-center gap-4 px-4 py-3 hover:bg-saffron-50 sm:px-5">
                                    <span class="grid size-12 shrink-0 place-items-center overflow-hidden rounded-md bg-mist">
                                        <img x-show="result.image" :src="result.image" alt="" class="size-full object-cover">
                                        <img x-show="! result.image" src="{{ asset('favicon.svg') }}" alt="" class="w-1/2 opacity-35">
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate font-medium text-ink" x-text="result.name"></span>
                                        <span class="block truncate text-xs text-navy-500" x-text="result.meta"></span>
                                    </span>
                                    <span class="shrink-0 text-sm font-semibold text-navy-800" x-text="result.price"></span>
                                </a>
                            </li>
                        </template>
                    </ul>

                    <div x-show="searched && ! loading && ! results.length" class="px-5 py-6 text-sm">
                        <p class="font-medium text-ink" x-text="@js(wording('search_none_title')).replace(':term', term.trim())"></p>
                        <p class="mt-1 text-ink/70">{{ wording('search_none_text') }} <a href="{{ route('contact') }}" class="font-medium text-saffron-700 underline underline-offset-2">{{ wording('list_empty_button') }}</a></p>
                    </div>

                    <a x-show="results.length" :href="allUrl" class="sticky bottom-0 block border-t border-navy-200 bg-mist px-5 py-3 text-sm font-semibold text-saffron-700 hover:text-saffron-800">
                        <span x-text="@js(wording('search_all_results')).replace(':term', term.trim())"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
