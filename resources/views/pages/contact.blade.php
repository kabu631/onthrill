@php
    $title = wording('contact_title');

    /*
     * Map: the embed link from Site Settings, otherwise a map of the office address,
     * the office coordinates, or the "map location" (Site Settings > Contact & Social).
     */
    $lat = setting('geo_latitude');
    $lng = setting('geo_longitude');
    $place = setting('address') ?: (filled($lat) && filled($lng) ? $lat.','.$lng : setting('map_location'));
    $map = setting('map_embed_url') ?: (filled($place) ? 'https://maps.google.com/maps?q='.rawurlencode($place).'&z=14&output=embed' : null);
    $directions = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($place ?: setting('site_name'));
@endphp

<x-layout :title="$title" :description="wording('contact_intro')">
    <x-page-banner :title="$title" :crumbs="[$title => null]" :intro="wording('contact_intro')" />

    <div class="container-page section pt-8 sm:pt-10 space-y-8">
        {{-- Quick Contact Cards --}}
        <div data-reveal class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- 1. Call Us --}}
            <div class="card p-5 transition-all hover:border-navy-200 hover:shadow-md flex flex-col justify-between">
                <div>
                    <div class="grid size-10 place-items-center rounded-lg bg-navy-50 text-navy-800">
                        <x-op-icon name="phone" class="size-5" />
                    </div>
                    <p class="mt-3.5 text-[11px] font-bold uppercase tracking-wider text-navy-400">{{ wording('contact_label_phone') }}</p>
                    <p class="mt-1 text-base font-bold text-navy-950">
                        @if (filled(setting('phone')))
                            <a href="tel:{{ phone_digits(setting('phone')) }}" class="hover:text-saffron-600 transition-colors">{{ setting('phone') }}</a>
                        @else
                            Available by phone
                        @endif
                    </p>
                </div>
                <p class="mt-3 text-xs text-ink/65">{{ setting('business_hours') ?: 'Sun – Fri: 10 AM – 6 PM' }}</p>
            </div>

            {{-- 2. WhatsApp --}}
            <div class="card p-5 transition-all hover:border-emerald-200 hover:shadow-md flex flex-col justify-between">
                <div>
                    <div class="grid size-10 place-items-center rounded-lg bg-emerald-50 text-emerald-600">
                        <x-op-icon name="whatsapp" class="size-5" />
                    </div>
                    <p class="mt-3.5 text-[11px] font-bold uppercase tracking-wider text-navy-400">{{ wording('contact_label_whatsapp') }}</p>
                    <p class="mt-1 text-base font-bold text-navy-950">
                        @if (filled(setting('whatsapp') ?: setting('phone')))
                            <a href="{{ whatsapp_url(wording('whatsapp_greeting')) }}" target="_blank" rel="noopener" class="hover:text-emerald-700 transition-colors">
                                {{ setting('whatsapp') ?: setting('phone') }}
                            </a>
                        @else
                            Direct Chat
                        @endif
                    </p>
                </div>
                <div class="mt-3">
                    @if (filled(setting('whatsapp') ?: setting('phone')))
                        <a href="{{ whatsapp_url(wording('whatsapp_greeting')) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 hover:text-emerald-800 transition-colors">
                            {{ wording('contact_whatsapp_button') }} <x-op-icon name="arrow-right" class="size-3" />
                        </a>
                    @else
                        <span class="text-xs text-ink/65">Fast response</span>
                    @endif
                </div>
            </div>

            {{-- 3. Email --}}
            <div class="card p-5 transition-all hover:border-saffron-200 hover:shadow-md flex flex-col justify-between">
                <div>
                    <div class="grid size-10 place-items-center rounded-lg bg-saffron-50 text-saffron-700">
                        <x-op-icon name="mail" class="size-5" />
                    </div>
                    <p class="mt-3.5 text-[11px] font-bold uppercase tracking-wider text-navy-400">{{ wording('contact_label_email') }}</p>
                    <p class="mt-1 text-base font-bold text-navy-950">
                        <a href="mailto:{{ setting('email') }}" class="hover:text-saffron-600 transition-colors break-all">
                            {{ setting('email') ?: 'info@onthrill.com' }}
                        </a>
                    </p>
                </div>
                <p class="mt-3 text-xs text-ink/65">Usually replied same day</p>
            </div>

            {{-- 4. Location --}}
            <div class="card p-5 transition-all hover:border-navy-200 hover:shadow-md flex flex-col justify-between">
                <div>
                    <div class="grid size-10 place-items-center rounded-lg bg-navy-50 text-navy-800">
                        <x-op-icon name="map-pin" class="size-5" />
                    </div>
                    <p class="mt-3.5 text-[11px] font-bold uppercase tracking-wider text-navy-400">{{ wording('contact_label_address') }}</p>
                    <p class="mt-1 text-base font-bold text-navy-950">
                        {{ setting('address') ?: (setting('map_location') ?: 'Kathmandu, Nepal') }}
                    </p>
                </div>
                <p class="mt-3 text-xs text-ink/65">Nepal Tour Operations</p>
            </div>
        </div>

        {{-- Main Two-Column Section: Form + Map --}}
        <div data-reveal-group class="grid gap-8 lg:grid-cols-12 items-stretch">
            {{-- Left: Enquiry Form (7 cols) --}}
            <div class="card p-6 sm:p-8 lg:col-span-7 flex flex-col justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-navy-950">{{ wording('contact_form_title') }}</h2>
                    <p class="mt-1 text-sm text-ink/75">{{ wording('contact_required') }}</p>
                    @include('partials.inquiry-form', ['packages' => $packages])
                </div>

                {{-- Trust Assurance Line --}}
                <div class="mt-6 pt-5 border-t border-navy-100 flex items-center gap-2 text-xs text-navy-500">
                    <x-op-icon name="shield" class="size-4 text-emerald-600 shrink-0" />
                    <span>Your contact details are strictly confidential and used solely to arrange your tour.</span>
                </div>
            </div>

            {{-- Right: Office & Interactive Map Card (5 cols) --}}
            <div class="card flex flex-col overflow-hidden lg:col-span-5">
                <div class="p-6 pb-4 border-b border-navy-100 bg-mist/30">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="inline-flex items-center gap-1 rounded-full bg-saffron-50 px-2.5 py-0.5 text-xs font-semibold text-saffron-700">
                                <x-op-icon name="map-pin" class="size-3" /> Head Office
                            </span>
                            <h3 class="mt-2 text-xl font-bold text-navy-950">{{ setting('site_name') }}</h3>
                            <p class="text-xs text-navy-600 mt-0.5">{{ $place ?: 'Kathmandu, Nepal' }}</p>
                        </div>
                        @if ($directions)
                            <a href="{{ $directions }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm shrink-0">
                                <x-op-icon name="map-pin" class="size-3.5 text-saffron-600" /> {{ wording('contact_directions') }}
                            </a>
                        @endif
                    </div>
                </div>

                @if ($map)
                    <div class="relative flex-1 min-h-[340px] sm:min-h-[380px] bg-navy-50">
                        <iframe src="{{ $map }}" title="{{ wording('contact_map_title') }}: {{ setting('site_name') }}" loading="lazy" class="absolute inset-0 size-full border-0" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    </div>
                @endif

                <div class="border-t border-navy-100 bg-mist/50 px-6 py-3.5 text-xs text-navy-700 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <x-op-icon name="clock" class="size-3.5 text-navy-500" /> {{ setting('business_hours') ?: 'Sun – Fri: 10 AM – 6 PM' }}
                    </span>
                    <span class="font-medium text-saffron-700">Visit us in Kathmandu</span>
                </div>
            </div>
        </div>
    </div>
</x-layout>
