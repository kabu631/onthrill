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

    <div class="container-page section pt-8 sm:pt-10">
        <div data-reveal-group class="space-y-8">
            <div class="grid gap-8 lg:grid-cols-[22rem_1fr] items-start">
                <div class="space-y-6">
                    <div class="card p-6">
                        <h2 class="text-xl font-bold">{{ setting('site_name') }}</h2>
                        <dl class="mt-4 divide-y divide-navy-100 text-sm">
                            @foreach ([
                                ['label' => wording('contact_label_phone'), 'value' => setting('phone'), 'href' => 'tel:'.phone_digits(setting('phone'))],
                                ['label' => wording('contact_label_whatsapp'), 'value' => setting('whatsapp'), 'href' => whatsapp_url(wording('whatsapp_greeting')), 'blank' => true],
                                ['label' => wording('contact_label_email'), 'value' => setting('email'), 'href' => 'mailto:'.setting('email')],
                                ['label' => wording('contact_label_address'), 'value' => setting('address'), 'href' => null],
                                ['label' => wording('contact_label_hours'), 'value' => setting('business_hours'), 'href' => null],
                            ] as $item)
                                @continue(blank($item['value']))
                                <div class="py-3.5 first:pt-0 last:pb-0">
                                    <dt class="text-navy-500">{{ $item['label'] }}</dt>
                                    <dd class="mt-0.5 text-base font-medium break-words text-ink">
                                        @if ($item['href'])
                                            <a href="{{ $item['href'] }}" @if (! empty($item['blank'])) target="_blank" rel="noopener" @endif class="hover:text-saffron-700">{{ $item['value'] }}</a>
                                        @else
                                            {{ $item['value'] }}
                                        @endif
                                    </dd>
                                </div>
                            @endforeach
                        </dl>
                        @if (filled(setting('whatsapp') ?: setting('phone')))
                            <a href="{{ whatsapp_url(wording('whatsapp_greeting')) }}" target="_blank" rel="noopener" class="btn btn-whatsapp mt-6 w-full"><x-op-icon name="whatsapp" /> {{ wording('contact_whatsapp_button') }}</a>
                        @endif
                    </div>

                    {{-- Helpful reassurance box --}}
                    <div class="rounded-xl border border-navy-100 bg-mist/60 p-6">
                        <h3 class="font-display text-base font-semibold text-navy-950">Quick Assistance</h3>
                        <p class="mt-1 text-sm text-ink/75">Planning a customized pilgrimage or cultural tour? We are here to help you every step of the way.</p>
                        <ul class="mt-4 space-y-2.5 text-xs font-medium text-navy-900">
                            <li class="flex items-center gap-2.5">
                                <span class="grid size-5 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                                    <x-op-icon name="check" class="size-3" />
                                </span>
                                Quick response, usually within the same day
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="grid size-5 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                                    <x-op-icon name="check" class="size-3" />
                                </span>
                                Custom itineraries for families & groups
                            </li>
                            <li class="flex items-center gap-2.5">
                                <span class="grid size-5 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                                    <x-op-icon name="check" class="size-3" />
                                </span>
                                Transparent pricing in INR & NPR
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Form on the right (ends naturally, no extra empty white space!) --}}
                <div class="card p-6 sm:p-8">
                    <h2 class="text-2xl font-bold">{{ wording('contact_form_title') }}</h2>
                    <p class="mt-1 text-sm">{{ wording('contact_required') }}</p>
                    @include('partials.inquiry-form', ['packages' => $packages])
                </div>
            </div>

            @if ($map)
                {{-- Full-width map section --}}
                <section class="card overflow-hidden" aria-label="{{ wording('contact_map_title') }}">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-navy-100 px-6 py-4 gap-3 bg-mist/40">
                        <div>
                            <h3 class="font-display text-lg font-bold text-navy-900">{{ wording('contact_map_title') }}</h3>
                            <p class="text-xs text-navy-600 mt-0.5">{{ $place ?: setting('site_name') }}</p>
                        </div>
                        <a href="{{ $directions }}" target="_blank" rel="noopener" class="btn btn-secondary btn-sm shrink-0 self-start sm:self-auto">
                            <x-op-icon name="map-pin" class="size-4 text-saffron-600" /> {{ wording('contact_directions') }}
                        </a>
                    </div>
                    <iframe src="{{ $map }}" title="{{ wording('contact_map_title') }}: {{ setting('site_name') }}" loading="lazy" class="h-80 w-full border-0" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                </section>
            @endif
        </div>
    </div>
</x-layout>
