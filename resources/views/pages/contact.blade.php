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
        {{--
            Large screens: contact details with the map under them on the left, the form on the
            right; the map stretches so both columns end level. Phones: details, form, then map.
        --}}
        <div data-reveal-group class="grid gap-x-8 gap-y-6 lg:grid-cols-[22rem_1fr] lg:grid-rows-[auto_1fr]">
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

            <div class="card p-6 sm:p-8 lg:col-start-2 lg:row-span-2 lg:row-start-1">
                <h2 class="text-2xl font-bold">{{ wording('contact_form_title') }}</h2>
                <p class="mt-1 text-sm">{{ wording('contact_required') }}</p>
                @include('partials.inquiry-form', ['packages' => $packages])
            </div>

            @if ($map)
                {{-- Loads only as the visitor scrolls down to it. --}}
                <section class="card flex min-h-80 flex-col overflow-hidden lg:col-start-1 lg:row-start-2" aria-label="{{ wording('contact_map_title') }}">
                    <iframe src="{{ $map }}" title="{{ wording('contact_map_title') }}: {{ setting('site_name') }}" loading="lazy" class="min-h-64 w-full flex-1 border-0" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    <a href="{{ $directions }}" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 border-t border-navy-100 px-4 py-3 text-sm font-semibold text-navy-800 transition-colors hover:text-saffron-700">
                        <x-op-icon name="map-pin" class="size-4 text-saffron-600" /> {{ wording('contact_directions') }}
                    </a>
                </section>
            @endif
        </div>
    </div>
</x-layout>
