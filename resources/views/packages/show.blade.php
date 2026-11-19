@php
    $p = $package;
    $crumbs = [wording('packages_title') => route('packages.index')];
    if ($p->category) {
        $crumbs[$p->category->name] = $p->category->url();
    }
    $crumbs[$p->name] = null;

    // The package's own photos, then those of the places it visits (each with its credit).
    $photos = collect([$p->image, ...($p->gallery ?? [])])->filter()
        ->map(fn ($path) => ['path' => $path, 'alt' => $p->name, 'destination' => $p->destinations->firstWhere('image', $path)])
        ->concat($p->destinations->filter(fn ($d) => filled($d->image))->map(fn ($d) => ['path' => $d->image, 'alt' => $d->name, 'destination' => $d]))
        ->unique('path')->take(8)->values()
        ->map(fn ($photo) => [
            'path' => $photo['path'],
            'alt' => $photo['destination']?->name ?? $photo['alt'],
            'credit' => $photo['destination']?->image_credit,
            'credit_url' => $photo['destination']?->image_credit_url,
        ])->all();

    $faqs = array_values(array_filter((array) $p->faqs, fn ($f) => filled($f['question'] ?? null)));
    $facts = array_filter([
        ['icon' => 'clock', 'label' => wording('pkg_fact_duration'), 'value' => $p->durationLabel()],
        $p->start_point ? ['icon' => 'map-pin', 'label' => wording('pkg_fact_route'), 'value' => $p->start_point === $p->end_point ? $p->start_point : $p->start_point.' / '.$p->end_point] : null,
        $p->best_season ? ['icon' => 'sun', 'label' => wording('pkg_fact_season'), 'value' => $p->best_season] : null,
        $p->group_size ? ['icon' => 'users', 'label' => wording('pkg_fact_group'), 'value' => $p->group_size] : null,
        $p->difficulty ? ['icon' => 'check-circle', 'label' => wording('pkg_fact_difficulty'), 'value' => $p->difficulty] : null,
        $p->max_altitude_m ? ['icon' => 'mountain', 'label' => wording('pkg_fact_altitude'), 'value' => number_format($p->max_altitude_m).' m'] : null,
    ]);
    $placeNames = $p->destinations->pluck('name');
    $advance = (float) setting('advance_percent');
    $canBook = Gate::allows('book');
    $nationality = old('nationality', $currency === 'NPR' ? 'nepali' : 'indian');
    $currencyName = fn (string $code) => wording($code === 'NPR' ? 'currency_npr_name' : 'currency_inr_name');
    $bookLabel = $p->isOnRequest() ? wording('price_request_button') : wording('price_book_button');

    // Section tabs: only the sections this package has.
    $tabs = array_filter([
        'overview' => wording('pkg_tab_overview'),
        'itinerary' => $p->itinerary ? wording('pkg_tab_itinerary') : null,
        'included' => ($p->inclusions || $p->exclusions) ? wording('pkg_tab_included') : null,
        'before-you-go' => $p->notes ? wording('pkg_tab_before') : null,
        'places' => $p->destinations->isNotEmpty() ? wording('pkg_tab_places') : null,
        'faqs' => $faqs ? wording('pkg_tab_faqs') : null,
        'book' => $canBook ? wording('pkg_tab_book') : null,
    ]);
    $priceLine = $p->isOnRequest() ? null : money($p->price($currency), $currency);
    $heading = 'text-2xl font-bold sm:text-3xl';
@endphp

<x-layout :title="$p->meta_title ?: $p->name" :description="$p->meta_description ?: $p->summary" :image="media_url($p->image)">
    @push('schema')
        <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::breadcrumbs($crumbs)) !!}</script>
        <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::package($p)) !!}</script>
        @if ($faqs)
            <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::faq($faqs)) !!}</script>
        @endif
    @endpush
    @push('head')
        {{-- Room for the price bar fixed to the bottom of the screen on phones --}}
        <style>@media (max-width: 1023px) { :root { --bottom-bar: 4.25rem; } body { padding-bottom: var(--bottom-bar); } }</style>
    @endpush

    {{-- Title, the places on the tour and the price --}}
    <section class="border-b border-navy-100 bg-mist">
        <div class="container-page py-8 sm:py-10">
            <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-ink/60 sm:text-sm">
                <a href="{{ route('home') }}" class="hover:text-saffron-700">{{ wording('breadcrumb_home') }}</a>
                @foreach ($crumbs as $label => $url)
                    <x-op-icon name="chevron-right" class="size-3 text-ink/35" />
                    @if ($url)
                        <a href="{{ $url }}" class="hover:text-saffron-700">{{ $label }}</a>
                    @else
                        <span class="text-ink/85" aria-current="page">{{ $label }}</span>
                    @endif
                @endforeach
            </nav>

            <div class="mt-5 flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-3xl">
                    <h1 class="anim-rise text-3xl leading-tight font-bold sm:text-4xl lg:text-[2.75rem]">{{ $p->name }}</h1>
                    <ul style="--d: 80ms" class="anim-rise mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm text-ink/75">
                        <li class="flex items-center gap-1.5"><x-op-icon name="clock" class="size-4 text-saffron-600" /> {{ $p->durationLabel() }}</li>
                        @if ($placeNames->isNotEmpty())
                            <li class="flex items-center gap-1.5"><x-op-icon name="map-pin" class="size-4 text-saffron-600" /> {{ $placeNames->take(4)->implode(', ') }}@if ($placeNames->count() > 4) +{{ $placeNames->count() - 4 }} @endif</li>
                        @endif
                        @if ($p->category)
                            <li><a href="{{ $p->category->url() }}" class="flex items-center gap-1.5 hover:text-saffron-700"><x-op-icon name="map" class="size-4 text-saffron-600" /> {{ $p->category->name }}</a></li>
                        @endif
                    </ul>
                </div>
                <div style="--d: 120ms" class="anim-rise hidden text-right lg:block">
                    @if ($priceLine)
                        <p class="text-sm text-ink/60">{{ wording('price_from') }}</p>
                        <p class="font-display text-4xl leading-none font-bold text-navy-900">{{ $priceLine }}</p>
                        <p class="mt-1 text-sm text-ink/60">{{ wording('price_per_person') }}</p>
                    @else
                        <p class="font-display text-2xl font-semibold text-navy-900">{{ wording('price_on_request') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @if ($photos)
        <div data-reveal class="container-page mt-8">
            @include('partials.photo-grid', ['photos' => $photos])
        </div>
    @endif

    {{-- Section tabs, kept under the header while reading --}}
    <nav x-data="sectionNav" aria-label="{{ $p->name }}" class="sticky top-16 z-30 mt-8 border-y border-navy-100 bg-white/95 backdrop-blur lg:top-20">
        <div class="container-page">
            <ul class="no-scrollbar -mx-4 flex gap-1 overflow-x-auto px-4 text-sm font-medium sm:mx-0 sm:px-0">
                @foreach ($tabs as $id => $label)
                    <li>
                        <a href="#{{ $id }}" class="block border-b-2 px-3 py-4 whitespace-nowrap transition-colors"
                           :class="active === '{{ $id }}' ? 'border-saffron-600 text-saffron-700' : 'border-transparent text-ink/70 hover:text-navy-900'">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>

    <div class="container-page pt-10 pb-16 sm:pb-20">
        <div class="grid gap-12 lg:grid-cols-[1fr_22rem]">
            <article class="min-w-0 space-y-14">
                {{-- Overview --}}
                <section id="overview" data-reveal class="scroll-mt-40">
                    <h2 class="{{ $heading }}">{{ wording('pkg_overview_title') }}</h2>
                    <span class="title-rule"></span>

                    <dl class="mt-7 grid grid-cols-2 gap-x-6 gap-y-5 rounded-lg border border-navy-100 p-5 sm:grid-cols-3 sm:p-6">
                        @foreach ($facts as $fact)
                            <div class="flex items-start gap-3">
                                <x-op-icon :name="$fact['icon']" class="mt-0.5 size-5 text-saffron-600" />
                                <div class="min-w-0">
                                    <dt class="text-xs text-navy-500">{{ $fact['label'] }}</dt>
                                    <dd class="mt-0.5 text-sm font-semibold text-navy-900">{{ $fact['value'] }}</dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>

                    @if ($p->overview)
                        <div class="rich mt-7">{!! clean_html($p->overview) !!}</div>
                    @elseif ($p->summary)
                        <p class="mt-7 leading-relaxed">{{ $p->summary }}</p>
                    @endif

                    @if ($p->highlights)
                        <h3 class="mt-9 text-xl font-semibold">{{ wording('pkg_highlights_title') }}</h3>
                        <ul class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                            @foreach (array_slice($p->highlights, 0, 10) as $highlight)
                                <li class="flex gap-3 text-[15px]"><x-op-icon name="check" class="mt-1 size-4 text-saffron-600" /> {{ $highlight }}</li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                {{-- Day by day --}}
                @if ($p->itinerary)
                    <section id="itinerary" data-reveal class="scroll-mt-40" x-data="{ all: false, toggleAll() { this.all = !this.all; this.$el.querySelectorAll('details').forEach((d) => d.open = this.all); } }">
                        <div class="flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <h2 class="{{ $heading }}">{{ wording('pkg_itinerary_title') }}</h2>
                                <span class="title-rule"></span>
                            </div>
                            <button type="button" class="text-sm font-semibold text-saffron-700 hover:text-saffron-800" @click="toggleAll()"
                                    x-text="all ? @js(wording('pkg_close_all')) : @js(wording('pkg_open_all'))">{{ wording('pkg_open_all') }}</button>
                        </div>

                        <ol class="relative mt-7 space-y-3 before:absolute before:top-4 before:bottom-4 before:left-[1.65rem] before:w-px before:bg-navy-200 sm:before:left-[2.15rem]">
                            @foreach ($p->itinerary as $day)
                                <li class="relative">
                                    <details class="group rounded-lg border border-navy-100 bg-white open:shadow-card" @if ($loop->first) open @endif>
                                        <summary class="flex cursor-pointer list-none items-center gap-4 p-3 [&::-webkit-details-marker]:hidden sm:p-4">
                                            <span class="grid w-12 shrink-0 place-items-center rounded-md bg-navy-900 py-1.5 text-center text-white group-open:bg-saffron-600 sm:w-14">
                                                <span class="text-[11px] leading-none font-medium opacity-80">{{ wording('pkg_day') }}</span>
                                                <span class="font-display text-lg leading-tight font-bold">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                            </span>
                                            <span class="min-w-0 flex-1 font-display text-[17px] font-semibold text-navy-900">{{ $day['title'] ?? '' }}</span>
                                            <x-op-icon name="chevron-down" class="size-5 text-ink/45 group-open:rotate-180" />
                                        </summary>
                                        @if (filled($day['description'] ?? null))
                                            <p class="pr-5 pb-5 pl-[4.75rem] leading-relaxed sm:pl-[5.75rem]">{{ $day['description'] }}</p>
                                        @endif
                                    </details>
                                </li>
                            @endforeach
                        </ol>
                    </section>
                @endif

                {{-- Included / not included --}}
                @if ($p->inclusions || $p->exclusions)
                    <section id="included" data-reveal class="scroll-mt-40">
                        <h2 class="{{ $heading }}">{{ wording('pkg_included_title') }}</h2>
                        <span class="title-rule"></span>
                        <div class="mt-7 grid gap-5 sm:grid-cols-2">
                            @if ($p->inclusions)
                                <div class="rounded-lg border border-navy-100 p-6">
                                    <h3 class="flex items-center gap-2 text-lg font-semibold"><x-op-icon name="check-circle" class="size-5 text-green-700" /> {{ wording('pkg_included') }}</h3>
                                    <ul class="mt-4 space-y-2.5 text-sm">
                                        @foreach ($p->inclusions as $item)
                                            <li class="flex gap-2.5"><x-op-icon name="check" class="mt-0.5 size-4 shrink-0 text-green-700" /> {{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            @if ($p->exclusions)
                                <div class="rounded-lg border border-navy-100 p-6">
                                    <h3 class="flex items-center gap-2 text-lg font-semibold"><x-op-icon name="x" class="size-5 text-red-700" /> {{ wording('pkg_excluded') }}</h3>
                                    <ul class="mt-4 space-y-2.5 text-sm">
                                        @foreach ($p->exclusions as $item)
                                            <li class="flex gap-2.5"><x-op-icon name="x" class="mt-0.5 size-4 shrink-0 text-ink/40" /> {{ $item }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </section>
                @endif

                @if ($p->notes)
                    <section id="before-you-go" data-reveal class="scroll-mt-40">
                        <h2 class="{{ $heading }}">{{ wording('pkg_before_title') }}</h2>
                        <span class="title-rule"></span>
                        <div class="rich mt-7 rounded-lg border-l-4 border-saffron-500 bg-saffron-50/60 p-6">{!! clean_html($p->notes) !!}</div>
                    </section>
                @endif

                @if ($p->destinations->isNotEmpty())
                    <section id="places" data-reveal class="scroll-mt-40">
                        <h2 class="{{ $heading }}">{{ wording('pkg_places_title') }}</h2>
                        <span class="title-rule"></span>
                        <ul class="mt-7 grid gap-4 sm:grid-cols-2">
                            @foreach ($p->destinations as $destination)
                                <li>
                                    <a href="{{ $destination->url() }}" class="group flex items-center gap-4 rounded-lg border border-navy-100 p-3 transition-colors hover:border-saffron-300">
                                        <span class="size-20 shrink-0 overflow-hidden rounded-md bg-mist"><x-media :path="$destination->image" :width="160" :alt="$destination->name" /></span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block font-display text-[17px] font-semibold text-navy-900 group-hover:text-saffron-700">{{ $destination->name }}</span>
                                            <span class="mt-0.5 block text-xs text-ink/60">{{ \Illuminate\Support\Str::before((string) $destination->region, ',') }}, {{ \Illuminate\Support\Str::before($destination->country, ' (') }}@if ($destination->altitude_m) · {{ number_format($destination->altitude_m) }} m @endif</span>
                                        </span>
                                        <x-op-icon name="arrow-right" class="mr-2 size-4 text-saffron-600" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($faqs)
                    <section id="faqs" data-reveal class="scroll-mt-40">
                        <h2 class="{{ $heading }}">{{ wording('pkg_faq_title') }}</h2>
                        <span class="title-rule"></span>
                        <div class="mt-7 space-y-3">
                            @foreach ($faqs as $faq)
                                <details class="group rounded-lg border border-navy-100 bg-white open:shadow-card">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-display text-[17px] font-semibold text-navy-900 [&::-webkit-details-marker]:hidden">
                                        {{ $faq['question'] }}
                                        <span class="grid size-7 shrink-0 place-items-center rounded-md bg-mist text-saffron-600 group-open:bg-saffron-600 group-open:text-white"><x-op-icon name="plus" class="size-4 group-open:rotate-45" /></span>
                                    </summary>
                                    <p class="px-5 pb-5 leading-relaxed">{{ $faq['answer'] ?? '' }}</p>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @endif

                {{-- Booking request --}}
                @if ($canBook)
                    <section id="book" data-reveal class="card scroll-mt-40 p-6 sm:p-8"
                             x-data="bookingEstimate({ inr: @js($p->price('INR')), npr: @js($p->price('NPR')), advance: @js($advance), other: @js($currency), adults: @js((int) old('adults', 2)), children: @js((int) old('children', 0)), nationality: @js($nationality) })">
                        <h2 class="{{ $heading }}">{{ $p->isOnRequest() ? wording('booking_title_request') : wording('booking_title') }}</h2>
                        <span class="title-rule"></span>
                        <p class="mt-4 text-sm">{{ wording('booking_note') }}</p>

                        @if ($errors->booking->any())
                            <div role="alert" class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800">{{ wording('form_error') }}</div>
                        @endif

                        <form action="{{ route('bookings.store', $p) }}" method="post" class="mt-6 grid gap-4 sm:grid-cols-2" x-data="{ sending: false }" @submit="sending = true">
                            @csrf
                            <x-field name="travel_date" id="book-date" :label="wording('booking_date')" type="date" bag="booking" required :min="now()->toDateString()" />
                            <div class="grid grid-cols-2 gap-4">
                                <x-field name="adults" id="book-adults" :label="wording('booking_adults')" bag="booking" required>
                                    <select id="book-adults" name="adults" class="input" x-model.number="adults">
                                        @for ($i = 1; $i <= 20; $i++)
                                            <option value="{{ $i }}" @selected((int) old('adults', 2) === $i)>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </x-field>
                                <x-field name="children" id="book-children" :label="wording('booking_children')" bag="booking">
                                    <select id="book-children" name="children" class="input" x-model.number="children">
                                        @for ($i = 0; $i <= 10; $i++)
                                            <option value="{{ $i }}" @selected((int) old('children', 0) === $i)>{{ $i }}</option>
                                        @endfor
                                    </select>
                                </x-field>
                            </div>

                            <fieldset class="sm:col-span-2">
                                <legend class="label">{{ wording('booking_nationality') }} <span class="text-red-600" aria-hidden="true">*</span></legend>
                                <div class="flex flex-wrap gap-3">
                                    @foreach (\App\Models\Booking::NATIONALITIES as $value => $label)
                                        <label class="flex cursor-pointer items-center gap-2 rounded-md border px-4 py-2.5 text-sm" :class="nationality === '{{ $value }}' ? 'border-saffron-500 bg-saffron-50' : 'border-navy-200 bg-white'">
                                            <input type="radio" name="nationality" value="{{ $value }}" x-model="nationality" @checked($nationality === $value) class="size-4 accent-saffron-600"> {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                                <p class="mt-1.5 text-xs text-ink/55">{{ wording('booking_nationality_note') }}</p>
                                @error('nationality', 'booking')<p class="field-error">{{ $message }}</p>@enderror
                            </fieldset>

                            <x-field name="name" id="book-name" :label="wording('booking_name')" bag="booking" required autocomplete="name" maxlength="120" :value="auth()->user()?->name" />
                            <x-field name="phone" id="book-phone" :label="wording('booking_phone')" type="tel" bag="booking" required autocomplete="tel" inputmode="tel" maxlength="20" :value="auth()->user()?->phone" />
                            <x-field name="email" id="book-email" :label="wording('booking_email')" type="email" bag="booking" required autocomplete="email" maxlength="160" :value="auth()->user()?->email" :hint="wording('booking_email_hint')" />
                            <x-field name="travelling_from" id="book-from" :label="wording('booking_from')" bag="booking" maxlength="120" :placeholder="wording('booking_from_placeholder')" />
                            <x-field name="notes" id="book-notes" :label="wording('booking_notes')" bag="booking" class="sm:col-span-2">
                                <textarea id="book-notes" name="notes" rows="3" maxlength="2000" class="input" placeholder="{{ wording('booking_notes_placeholder') }}">{{ old('notes') }}</textarea>
                            </x-field>

                            @unless ($p->isOnRequest())
                                {{-- Live estimate; the final price is confirmed by phone. --}}
                                <div class="flex flex-wrap items-baseline justify-between gap-2 rounded-md border border-navy-100 bg-mist px-4 py-3 text-sm sm:col-span-2" x-show="total() > 0">
                                    <span>{{ wording('booking_estimate') }} <span x-text="adults + children"></span> <span x-text="adults + children === 1 ? @js(wording('booking_traveller')) : @js(wording('booking_travellers'))"></span></span>
                                    <span>
                                        <strong class="font-display text-lg text-navy-900" x-text="format(total())"></strong>
                                        <template x-if="advance > 0"><span class="text-ink/60"> · {{ wording('booking_advance') }} <strong class="text-ink" x-text="format(Math.round(total() * advance / 100))"></strong></span></template>
                                    </span>
                                </div>
                            @endunless

                            <div class="sm:col-span-2">
                                <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto" :disabled="sending">
                                    <span x-show="!sending">{{ wording('booking_button') }}</span>
                                    <span x-cloak x-show="sending">{{ wording('form_sending') }}</span>
                                </button>
                                <p class="mt-3 text-xs text-ink/55">{{ wording('booking_terms') }} <a href="{{ url('/terms-and-conditions') }}" class="underline">{{ wording('booking_terms_link') }}</a>.</p>
                            </div>
                        </form>
                    </section>
                @endif

                <p class="text-xs text-ink/55">{{ wording('last_updated', ['date' => $p->updated_at?->format('d F Y')]) }}</p>
            </article>

            {{-- Sidebar: the price, then an enquiry form about this tour that stays in view --}}
            <aside class="space-y-5">
                <div data-reveal class="card overflow-hidden">
                    <div class="bg-navy-900 px-6 py-5 text-white">
                        @if ($priceLine)
                            <p class="text-sm text-white/70">{{ wording('price_from') }}</p>
                            <p class="mt-0.5 flex flex-wrap items-baseline gap-x-2">
                                <span class="font-display text-3xl font-bold">{{ $priceLine }}</span>
                                @if ($p->isOnSale($currency))
                                    <span class="text-sm text-white/55 line-through">{{ money($p->regularPrice($currency), $currency) }}</span>
                                @endif
                            </p>
                            <p class="mt-0.5 text-sm text-white/70">{{ wording('price_per_person') }}
                                @if ($p->isOnSale($currency))
                                    <span class="badge ml-1 bg-saffron-600 text-white">{{ wording('price_off', ['percent' => $p->discountPercent($currency)]) }}</span>
                                @endif
                            </p>
                        @else
                            <p class="font-display text-2xl font-semibold">{{ wording('price_on_request') }}</p>
                            <p class="mt-1 text-sm text-white/70">{{ wording('price_request_note') }}</p>
                        @endif
                    </div>

                    <div class="p-6">
                        @unless ($p->isOnRequest())
                            <p class="text-xs text-ink/60">
                                {{ wording('price_shown_in', ['currency' => $currencyName($currency)]) }} ·
                                <a href="{{ route('currency', $currency === 'NPR' ? 'inr' : 'npr') }}" rel="nofollow" class="font-medium text-saffron-700 underline underline-offset-2">{{ wording('price_switch', ['currency' => $currency === 'NPR' ? '₹ INR' : 'NPR']) }}</a>
                            </p>
                            @if ($advance > 0)
                                <p class="mt-3 text-sm">{{ wording('price_advance', ['percent' => rtrim(rtrim(number_format($advance, 1), '0'), '.')]) }}</p>
                            @endif
                        @else
                            <p class="text-sm">{{ wording('price_request_text') }}</p>
                        @endunless

                        @if ($canBook)
                            <a href="#book" class="btn btn-primary btn-lg mt-5 w-full">{{ $bookLabel }}</a>
                        @else
                            <div class="mt-5">
                                @include('partials.staff-notice', ['editUrl' => auth()->user()?->can('update', $p) ? \App\Filament\Resources\Packages\PackageResource::getUrl('edit', ['record' => $p]) : null])
                            </div>
                        @endif
                        @if (filled(setting('whatsapp') ?: setting('phone')))
                            <a href="{{ whatsapp_url(wording('whatsapp_greeting').' '.$p->name) }}" target="_blank" rel="noopener" class="btn btn-whatsapp mt-3 w-full"><x-op-icon name="whatsapp" class="size-4" /> {{ wording('price_whatsapp') }}</a>
                        @endif

                        @include('partials.trust-points', ['points' => setting('package_trust_points')])
                    </div>
                </div>

                <div id="enquiry" data-reveal class="card scroll-mt-28 overflow-hidden lg:sticky lg:top-40">
                    <div class="border-b border-navy-100 px-6 pt-5 pb-4">
                        <h2 class="text-xl font-bold">{{ wording('pkg_enquiry_title') }}</h2>
                        <p class="mt-1 text-sm">{{ wording('pkg_enquiry_text') }}</p>
                    </div>
                    <div class="px-6 pb-6">
                        @include('partials.inquiry-form', ['package' => $p, 'compact' => true])
                    </div>
                </div>
            </aside>
        </div>
    </div>

    @if ($related->isNotEmpty())
        <section class="section bg-mist">
            <div class="container-page">
                <x-section-heading align="left" :title="wording('pkg_similar_title')" />
                <div data-reveal-group class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($related as $item)
                        <x-package-card :package="$item" :currency="$currency" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Price and booking button fixed to the bottom of the screen on phones --}}
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-navy-100 bg-white/95 px-4 py-3 shadow-lift backdrop-blur lg:hidden">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                @if ($priceLine)
                    <p class="text-xs text-ink/60">{{ wording('price_from') }} <span class="font-display text-lg font-bold text-navy-900">{{ $priceLine }}</span> {{ wording('price_per_person') }}</p>
                @else
                    <p class="font-display text-base font-semibold text-navy-900">{{ wording('price_on_request') }}</p>
                @endif
                <p class="truncate text-xs text-ink/55">{{ $p->durationLabel() }}</p>
            </div>
            @if ($canBook)
                <a href="#book" class="btn btn-primary shrink-0">{{ $p->isOnRequest() ? wording('price_request_button') : wording('mobile_book_button') }}</a>
            @else
                <a href="#enquiry" class="btn btn-outline shrink-0">{{ wording('pkg_enquiry_title') }}</a>
            @endif
        </div>
    </div>
</x-layout>
