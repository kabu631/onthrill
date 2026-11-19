@php
    $phone = setting('phone');
    $whatsapp = setting('whatsapp') ?: $phone;
    $email = setting('email');
    $hours = setting('business_hours');
    // On the homepage the menu bar starts transparent over the photo and turns white on scroll.
    $overHero = $overHero ?? false;
    // Staff (anyone with an admin role) get a shortcut to the admin panel.
    $isStaff = auth()->user()?->isStaff() ?? false;
    $social = collect(['facebook', 'instagram', 'youtube', 'twitter'])->mapWithKeys(fn ($n) => [$n => setting($n)])->filter();
    // Gallery, then any pages ticked "Show in header menu" (Website > Pages), then Contact.
    $staticLinks = [
        ['name' => wording('gallery_title'), 'url' => route('gallery', [], false)],
        ...$menu['header_pages'],
        ['name' => wording('menu_contact'), 'url' => route('contact', [], false)],
    ];
    $currentPath = '/'.ltrim(request()->path(), '/');
    $isCurrent = fn (string $url) => $url === $currentPath || ($url !== '/' && str_starts_with($currentPath, rtrim($url, '/').'/'));
    $dropdown = 'invisible absolute top-full z-50 opacity-0 transition-opacity duration-150 group-focus-within:visible group-focus-within:opacity-100 group-hover:visible group-hover:opacity-100';
    $navLink = 'nav-link flex h-full items-center gap-1 px-3 text-[15px] font-medium transition-colors hover:text-saffron-600';
    $places = fn (int $count) => wording($count === 1 ? 'menu_places_one' : 'menu_places_many', ['count' => $count]);
@endphp

{{-- Top bar: phone, WhatsApp, email, social links, prices currency and account (large screens) --}}
<div class="hidden bg-navy-950 text-[13px] text-white/75 lg:block">
    <div class="container-page flex h-10 items-center justify-between gap-6">
        <ul class="flex min-w-0 items-center gap-6">
            @if ($phone)
                <li><a href="tel:{{ phone_digits($phone) }}" class="flex items-center gap-2 hover:text-white"><x-op-icon name="phone" class="size-4 text-saffron-400" /> {{ $phone }}</a></li>
            @endif
            @if ($whatsapp)
                <li><a href="{{ whatsapp_url(wording('whatsapp_greeting')) }}" target="_blank" rel="noopener" class="flex items-center gap-2 hover:text-white"><x-op-icon name="whatsapp" class="size-4 text-whatsapp" /> {{ $whatsapp }}</a></li>
            @endif
            @if ($email)
                <li><a href="mailto:{{ $email }}" class="flex items-center gap-2 hover:text-white"><x-op-icon name="mail" class="size-4 text-saffron-400" /> {{ $email }}</a></li>
            @endif
            @if ($hours)
                <li class="hidden items-center gap-2 2xl:flex"><x-op-icon name="clock" class="size-4 text-saffron-400" /> {{ $hours }}</li>
            @endif
        </ul>

        <div class="flex shrink-0 items-center gap-5">
            @if ($social->isNotEmpty())
                <ul class="flex items-center gap-3">
                    @foreach ($social as $network => $url)
                        <li><a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}" class="hover:text-white"><x-op-icon :name="$network" class="size-4" /></a></li>
                    @endforeach
                </ul>
            @endif

            {{-- Prices in Indian or Nepali rupees --}}
            <div class="flex items-center gap-2 border-l border-white/20 pl-5" role="group" aria-label="{{ wording('top_prices_in') }}">
                <span class="text-white/60">{{ wording('top_prices_in') }}</span>
                @foreach (['INR' => '₹ INR', 'NPR' => 'NPR'] as $code => $label)
                    <a href="{{ route('currency', strtolower($code)) }}" rel="nofollow" @class(['rounded px-2 py-0.5 font-semibold', 'bg-white text-navy-900' => $currency === $code, 'hover:text-white' => $currency !== $code]) @if ($currency === $code) aria-current="true" @endif>{{ $label }}</a>
                @endforeach
            </div>

            <div class="flex items-center gap-4 border-l border-white/20 pl-5">
                @if ($isStaff)
                    <a href="{{ url('/admin') }}" class="flex items-center gap-1.5 font-semibold text-white hover:text-saffron-300"><x-op-icon name="shield" class="size-4" /> Admin Panel</a>
                @endif
                <a href="{{ auth()->check() ? route('account.index') : route('login') }}" class="flex items-center gap-1.5 hover:text-white">
                    <x-op-icon name="user" class="size-4" />
                    {{ auth()->check() ? wording('top_my_account') : wording('top_sign_in') }}
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Main bar: kept at the top while scrolling. White, or transparent over the homepage photo until scrolled. --}}
<header x-data="siteHeader(@js($overHero))" @class(['site-header sticky top-0 z-40', 'is-over-hero' => $overHero]) :class="{ 'is-solid': solid, 'is-scrolled': scrolled }">
    <div class="container-page relative flex h-16 items-center gap-4 lg:h-20">
        {{-- The logo leads to the homepage --}}
        <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ setting('site_name') }} home">
            <x-brand class="brand-dark" />
            <x-brand light class="brand-light" />
        </a>

        {{-- Main menu (large screens) --}}
        <nav class="ml-auto hidden h-full xl:block" aria-label="Main">
            <ul class="flex h-full items-stretch">
                {{-- Destinations: one column per country --}}
                <li class="group">
                    <a href="{{ route('destinations.index', [], false) }}" @class([$navLink, 'is-active text-saffron-600' => $isCurrent(route('destinations.index', [], false))])>
                        {{ wording('menu_destinations') }} <x-op-icon name="chevron-down" class="size-3.5 opacity-70" />
                    </a>
                    <div class="{{ $dropdown }} inset-x-0">
                        <div class="container-page">
                            <div class="grid grid-cols-4 gap-8 rounded-b-lg border-t-2 border-saffron-500 bg-white p-8 text-ink shadow-lift">
                                @foreach ($menu['countries'] as $item)
                                    @php $many = count($item['children']) > 7; @endphp
                                    <div @class(['col-span-2' => $many])>
                                        <a href="{{ $item['url'] }}" class="flex items-baseline justify-between border-b border-navy-100 pb-3 font-display text-lg font-semibold text-navy-900 hover:text-saffron-600">
                                            {{ $item['name'] }}
                                            <span class="text-xs font-normal text-ink/50">{{ $places(count($item['children'])) }}</span>
                                        </a>
                                        <ul @class(['mt-3 grid gap-x-6 text-sm', 'grid-cols-2' => $many])>
                                            @foreach ($item['children'] as $child)
                                                <li><a href="{{ $child['url'] }}" class="block py-1.5 text-ink/75 transition-colors hover:text-saffron-600">{{ $child['name'] }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                                <div class="col-span-full flex items-center justify-between border-t border-navy-100 pt-5 text-sm">
                                    <span class="text-ink/60">{{ wording('menu_destinations_note') }}</span>
                                    <a href="{{ route('destinations.index') }}" class="more-link">{{ wording('menu_all_destinations') }} <x-op-icon name="arrow-right" class="size-4" /></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>

                <li class="group relative">
                    <a href="{{ route('packages.index', [], false) }}" @class([$navLink, 'is-active text-saffron-600' => $isCurrent(route('packages.index', [], false))])>
                        {{ wording('menu_packages') }}
                        @if ($menu['tour_types'])
                            <x-op-icon name="chevron-down" class="size-3.5 opacity-70" />
                        @endif
                    </a>
                    @if ($menu['tour_types'])
                        <div class="{{ $dropdown }} left-0">
                            <ul class="w-72 rounded-b-lg border-t-2 border-saffron-500 bg-white p-2 text-sm text-ink shadow-lift">
                                @foreach ($menu['tour_types'] as $child)
                                    <li><a href="{{ $child['url'] }}" class="block rounded-md px-4 py-2.5 text-ink/80 hover:bg-mist hover:text-saffron-700">{{ $child['name'] }}</a></li>
                                @endforeach
                                <li class="mt-1 border-t border-navy-100 pt-1"><a href="{{ route('packages.index', [], false) }}" class="block rounded-md px-4 py-2.5 font-semibold text-saffron-700 hover:bg-mist">{{ wording('menu_all_packages') }}</a></li>
                            </ul>
                        </div>
                    @endif
                </li>

                @foreach ($staticLinks as $link)
                    <li><a href="{{ $link['url'] }}" @class([$navLink, 'is-active text-saffron-600' => $isCurrent($link['url'])])>{{ $link['name'] }}</a></li>
                @endforeach
            </ul>
        </nav>

        <div class="ml-auto flex items-center gap-1 xl:ml-2">
            <button type="button" class="rounded-md p-2.5 transition-colors hover:text-saffron-600" @click="$store.search.show()" aria-label="{{ wording('search_title') }}" title="{{ wording('search_title') }} (/)">
                <x-op-icon name="search" class="size-5" />
            </button>
            @can('book')
                <a href="{{ url(wording('menu_book_link')) }}" class="btn btn-primary ml-2 hidden sm:inline-flex">{{ wording('menu_book_label') }}</a>
            @endcan
            <button type="button" class="-mr-2 ml-1 rounded-md p-2 xl:hidden" @click="open = true" aria-label="Open menu">
                <x-op-icon name="menu" class="size-6" />
            </button>
        </div>
    </div>

    {{-- Mobile drawer --}}
    <div x-cloak x-show="open" class="fixed inset-0 z-50 text-ink xl:hidden" role="dialog" aria-modal="true" aria-label="Menu" @keydown.escape.window="open = false">
        <div x-show="open" x-transition.opacity class="absolute inset-0 bg-navy-950/60" @click="open = false"></div>

        <div x-show="open"
             x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
             x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full"
             class="absolute inset-y-0 right-0 flex w-[88%] max-w-sm flex-col bg-white shadow-lift">
            <div class="flex items-center justify-between border-b border-navy-100 px-4 py-3">
                <a href="{{ route('home') }}" aria-label="{{ setting('site_name') }} home"><x-brand /></a>
                <button type="button" class="rounded-md p-2 text-navy-900" @click="open = false" aria-label="Close menu">
                    <x-op-icon name="x" class="size-6" />
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto px-2 py-3 text-[15px] font-medium text-navy-900" aria-label="Mobile">
                <a href="{{ route('destinations.index') }}" class="block rounded-md px-3 py-3 hover:bg-mist">{{ wording('menu_all_destinations') }}</a>

                @foreach ($menu['countries'] as $index => $item)
                    <button type="button" class="flex w-full items-center justify-between rounded-md px-3 py-3 text-left hover:bg-mist" @click="toggle({{ $index }})" :aria-expanded="expanded === {{ $index }}">
                        {{ $item['name'] }}
                        <x-op-icon name="chevron-down" class="size-4" ::class="expanded === {{ $index }} && 'rotate-180'" />
                    </button>
                    <ul x-show="expanded === {{ $index }}" class="mb-1 ml-3 border-l-2 border-saffron-200 pl-2 text-sm font-normal text-ink/80">
                        <li><a href="{{ $item['url'] }}" class="block rounded-md px-3 py-2.5 font-medium text-saffron-700">{{ $item['name'] }}: {{ $places(count($item['children'])) }}</a></li>
                        @foreach ($item['children'] as $child)
                            <li><a href="{{ $child['url'] }}" class="block rounded-md px-3 py-2.5 hover:bg-mist">{{ $child['name'] }}</a></li>
                        @endforeach
                    </ul>
                @endforeach

                <button type="button" class="flex w-full items-center justify-between rounded-md px-3 py-3 text-left hover:bg-mist" @click="toggle('packages')" :aria-expanded="expanded === 'packages'">
                    {{ wording('menu_packages') }}
                    <x-op-icon name="chevron-down" class="size-4" ::class="expanded === 'packages' && 'rotate-180'" />
                </button>
                <ul x-show="expanded === 'packages'" class="mb-1 ml-3 border-l-2 border-saffron-200 pl-2 text-sm font-normal text-ink/80">
                    <li><a href="{{ route('packages.index') }}" class="block rounded-md px-3 py-2.5 font-medium text-saffron-700">{{ wording('menu_all_packages') }}</a></li>
                    @foreach ($menu['tour_types'] as $child)
                        <li><a href="{{ $child['url'] }}" class="block rounded-md px-3 py-2.5 hover:bg-mist">{{ $child['name'] }}</a></li>
                    @endforeach
                </ul>

                <div class="my-2 border-t border-navy-100"></div>
                @foreach ($staticLinks as $link)
                    <a href="{{ $link['url'] }}" class="block rounded-md px-3 py-3 hover:bg-mist">{{ $link['name'] }}</a>
                @endforeach
                <a href="{{ auth()->check() ? route('account.index') : route('login') }}" class="block rounded-md px-3 py-3 hover:bg-mist">{{ auth()->check() ? wording('top_my_account') : wording('top_sign_in') }}</a>
                @if ($isStaff)
                    <a href="{{ url('/admin') }}" class="flex items-center gap-2 rounded-md px-3 py-3 font-semibold text-saffron-700 hover:bg-mist"><x-op-icon name="shield" class="size-4" /> Admin Panel</a>
                @endif

                <div class="mt-3 flex items-center gap-2 px-3 text-sm">
                    <span class="text-ink/60">{{ wording('top_prices_in') }}</span>
                    @foreach (['INR' => '₹ INR', 'NPR' => 'NPR'] as $code => $label)
                        <a href="{{ route('currency', strtolower($code)) }}" rel="nofollow" @class(['rounded-md border px-3 py-1.5 text-xs font-semibold', 'border-navy-900 bg-navy-900 text-white' => $currency === $code, 'border-navy-200 text-navy-800' => $currency !== $code])>{{ $label }}</a>
                    @endforeach
                </div>
            </nav>

            <div class="space-y-2 border-t border-navy-100 p-3 text-sm">
                @if ($phone)
                    <a href="tel:{{ phone_digits($phone) }}" class="flex items-center gap-2 px-1 py-1 font-medium text-navy-900"><x-op-icon name="phone" class="size-4 text-saffron-600" /> {{ $phone }}</a>
                @endif
                @if ($email)
                    <a href="mailto:{{ $email }}" class="flex items-center gap-2 px-1 py-1 font-medium text-navy-900"><x-op-icon name="mail" class="size-4 text-saffron-600" /> {{ $email }}</a>
                @endif
                @can('book')
                    <a href="{{ url(wording('menu_book_link')) }}" class="btn btn-primary w-full">{{ wording('menu_book_label') }}</a>
                @endcan
                @if ($whatsapp)
                    <a href="{{ whatsapp_url(wording('whatsapp_greeting')) }}" target="_blank" rel="noopener" class="btn btn-whatsapp w-full"><x-op-icon name="whatsapp" class="size-4" /> WhatsApp {{ $whatsapp }}</a>
                @endif
            </div>
        </div>
    </div>
</header>

@include('partials.search-panel')
