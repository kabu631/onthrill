@php
    $countries = $destinations->pluck('country')->unique()->map(fn ($c) => \Illuminate\Support\Str::before($c, ' ('))->values();
    $countryList = $countries->count() > 1 ? $countries->slice(0, -1)->implode(', ').' and '.$countries->last() : $countries->first();
    $facts = array_values(array_filter([
        $destinations->isNotEmpty() ? ['icon' => 'map-pin', 'value' => $destinations->count(), 'label' => wording('fact_places')] : null,
        $packageCount ? ['icon' => 'map', 'value' => $packageCount, 'label' => wording('fact_packages')] : null,
        $countries->isNotEmpty() ? ['icon' => 'globe', 'value' => $countries->count(), 'label' => wording('fact_countries', ['list' => $countryList])] : null,
        ['icon' => 'banknotes', 'value' => wording('fact_currency_value'), 'label' => wording('fact_currency')],
    ]));
@endphp

{{-- What the site offers, in numbers. --}}
<section class="bg-white pt-14 pb-2 sm:pt-16" aria-label="At a glance">
    <div class="container-page">
        <dl data-reveal-group class="grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-4">
            @foreach ($facts as $fact)
                <div @class(['flex flex-col items-center text-center sm:flex-row sm:items-start sm:gap-4 sm:text-left', 'lg:border-l lg:border-navy-100 lg:pl-8' => ! $loop->first])>
                    <x-op-icon :name="$fact['icon']" class="mb-3 size-9 text-saffron-600 sm:mb-0" />
                    <div>
                        <dd class="font-display text-3xl leading-none font-bold text-navy-900 sm:text-4xl">{{ $fact['value'] }}</dd>
                        <dt class="mt-2 text-sm leading-snug">{{ $fact['label'] }}</dt>
                    </div>
                </div>
            @endforeach
        </dl>
    </div>
</section>
