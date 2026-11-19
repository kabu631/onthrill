@php
    $social = collect(['facebook', 'instagram', 'youtube', 'twitter'])
        ->mapWithKeys(fn ($network) => [$network => setting($network)])
        ->filter();
    $footerBackground = media_url(setting('footer_background'));
    $heading = 'font-display text-lg font-semibold text-white';
    $link = 'transition-colors hover:text-saffron-300';
@endphp

<footer class="relative isolate mt-auto overflow-hidden border-t-2 border-white/80 bg-navy-950 text-sm text-white/70">
    @if ($footerBackground)
        {{-- A Himalayan photograph, darkened so the small text stays readable. --}}
        <img src="{{ $footerBackground }}" alt="" loading="lazy" decoding="async" class="absolute inset-0 -z-20 size-full object-cover object-center opacity-60">
        <div class="absolute inset-0 -z-10 bg-navy-950/85" aria-hidden="true"></div>
    @endif

    <div data-reveal-group class="container-page grid gap-10 py-16 sm:grid-cols-2 lg:grid-cols-[1.5fr_1fr_1fr_1.3fr] lg:gap-12">
        <div>
            <a href="{{ route('home') }}" class="inline-block" aria-label="{{ setting('site_name') }} home">
                <x-brand light />
            </a>
            <p class="mt-6 max-w-xs leading-relaxed">{{ setting('footer_about') }}</p>

            @if ($social->isNotEmpty())
                <div class="mt-6 flex gap-2">
                    @foreach ($social as $network => $url)
                        <a href="{{ $url }}" target="_blank" rel="noopener" aria-label="{{ ucfirst($network) }}" class="grid size-9 place-items-center rounded-md border border-white/20 text-white transition-colors hover:border-saffron-500 hover:bg-saffron-600">
                            <x-op-icon :name="$network" class="size-4" />
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <h2 class="{{ $heading }}">{{ wording('footer_destinations_title') }}</h2>
            <span class="title-rule mt-3 w-8"></span>
            <ul class="mt-5 space-y-2.5">
                @foreach ($menu['countries'] as $item)
                    <li><a href="{{ $item['url'] }}" class="{{ $link }}">{{ $item['name'] }}</a></li>
                @endforeach
                <li><a href="{{ route('destinations.index') }}" class="{{ $link }}">{{ wording('menu_all_destinations') }}</a></li>
            </ul>

            <h2 class="{{ $heading }} mt-9">{{ wording('footer_packages_title') }}</h2>
            <span class="title-rule mt-3 w-8"></span>
            <ul class="mt-5 space-y-2.5">
                @foreach ($menu['tour_types'] as $item)
                    <li><a href="{{ $item['url'] }}" class="{{ $link }}">{{ $item['name'] }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h2 class="{{ $heading }}">{{ wording('footer_links_title') }}</h2>
            <span class="title-rule mt-3 w-8"></span>
            <ul class="mt-5 space-y-2.5">
                <li><a href="{{ route('packages.index') }}" class="{{ $link }}">{{ wording('footer_all_packages') }}</a></li>
                <li><a href="{{ route('gallery') }}" class="{{ $link }}">{{ wording('gallery_title') }}</a></li>
                @foreach ($menu['footer_pages'] as $page)
                    <li><a href="{{ $page['url'] }}" class="{{ $link }}">{{ $page['name'] }}</a></li>
                @endforeach
                <li><a href="{{ route('contact') }}" class="{{ $link }}">{{ wording('contact_title') }}</a></li>
            </ul>
        </div>

        <div>
            <h2 class="{{ $heading }}">{{ wording('footer_contact_title') }}</h2>
            <span class="title-rule mt-3 w-8"></span>
            <ul class="mt-5 space-y-4">
                @if (filled(setting('phone')))
                    <li class="flex gap-3">
                        <x-op-icon name="phone" class="mt-0.5 text-saffron-400" />
                        <span><span class="block text-xs text-white/50">{{ wording('footer_call_label') }}</span>
                            <a href="tel:{{ phone_digits(setting('phone')) }}" class="font-medium text-white {{ $link }}">{{ setting('phone') }}</a></span>
                    </li>
                @endif
                @if (filled(setting('email')))
                    <li class="flex gap-3">
                        <x-op-icon name="mail" class="mt-0.5 text-saffron-400" />
                        <span><span class="block text-xs text-white/50">{{ wording('footer_email_label') }}</span>
                            <a href="mailto:{{ setting('email') }}" class="font-medium break-all text-white {{ $link }}">{{ setting('email') }}</a></span>
                    </li>
                @endif
                @if (filled(setting('address')))
                    <li class="flex gap-3">
                        <x-op-icon name="map-pin" class="mt-0.5 text-saffron-400" />
                        <span><span class="block text-xs text-white/50">{{ wording('footer_office_label') }}</span>
                            <span class="font-medium text-white">{{ setting('address') }}</span></span>
                    </li>
                @endif
                @if (filled(setting('business_hours')))
                    <li class="flex gap-3">
                        <x-op-icon name="clock" class="mt-0.5 text-saffron-400" />
                        <span><span class="block text-xs text-white/50">{{ wording('footer_hours_label') }}</span>
                            <span class="font-medium text-white">{{ setting('business_hours') }}</span></span>
                    </li>
                @endif
            </ul>
            <a href="{{ route('contact') }}" class="btn btn-primary btn-sm mt-6">{{ wording('footer_button') }}</a>
        </div>
    </div>

    <div class="border-t border-white/10">
        {{-- On phones the text stays left of the floating back-to-top / WhatsApp buttons. --}}
        <div class="container-page flex flex-col items-start justify-between gap-1.5 py-5 pr-20 text-xs sm:flex-row sm:items-center sm:pr-6 lg:pr-8">
            <p>{{ wording('footer_copyright', ['year' => date('Y'), 'site' => setting('site_name')]) }}</p>
            <p class="text-white/50"><a href="{{ url('/photo-credits') }}" class="underline underline-offset-2 hover:text-white">{{ wording('footer_credits') }}</a></p>
        </div>
    </div>
</footer>
