{{--
    Enquiry form. Optional:
    - $package: the enquiry is about this tour (shown in the form, saved with the enquiry);
    - $destination: the enquiry is about this place;
    - $packages: a dropdown of tours (contact page);
    - $compact: single-column layout for sidebars.
    The tour or place appears with the enquiry in Admin > Bookings > Enquiries.
--}}
@php
    $compact = $compact ?? false;
    $grid = $compact ? '' : 'sm:grid-cols-2';
    $full = $compact ? '' : 'sm:col-span-2';
    $about = isset($package) ? ['label' => wording('enquiry_tour_label'), 'name' => $package->name, 'icon' => 'map']
        : (isset($destination) ? ['label' => wording('enquiry_place_label'), 'name' => $destination->name, 'icon' => 'map-pin'] : null);
@endphp

<form id="enquiry-form" action="{{ route('inquiry.store') }}" method="post" class="mt-5 grid scroll-mt-44 gap-4 {{ $grid }}"
      x-data="{ sending: false }" @submit="sending = true">
    @csrf

    @if (session('inquiry_sent'))
        <div role="status" class="flex items-start gap-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 {{ $full }}">
            <x-op-icon name="check-circle" class="mt-0.5" />
            <p>{{ session('inquiry_sent') }}</p>
        </div>
    @elseif ($errors->hasAny(['name', 'phone', 'email', 'message', 'package_id', 'destination_id']))
        <div role="alert" class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 {{ $full }}">
            {{ wording('form_error') }}
        </div>
    @endif

    {{-- Honeypot: hidden from people, tempting for bots --}}
    <div class="hidden" aria-hidden="true">
        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
    </div>

    {{-- What the enquiry is about: fixed on tour and destination pages --}}
    @if ($about)
        <div class="{{ $full }}">
            <p class="label">{{ $about['label'] }}</p>
            <p class="flex items-center gap-2.5 rounded-md border border-navy-200 bg-mist px-3.5 py-3 text-sm font-semibold text-navy-900">
                <x-op-icon :name="$about['icon']" class="size-4 text-saffron-600" /> {{ $about['name'] }}
            </p>
        </div>
        @isset($package)
            <input type="hidden" name="package_id" value="{{ $package->id }}">
        @else
            <input type="hidden" name="destination_id" value="{{ $destination->id }}">
        @endisset
    @endif

    <x-field name="name" :label="wording('enquiry_name')" required autocomplete="name" maxlength="120" />
    <x-field name="phone" :label="wording('enquiry_phone')" type="tel" required autocomplete="tel" inputmode="tel" maxlength="20" />
    <x-field name="email" :label="wording('enquiry_email')" type="email" autocomplete="email" maxlength="160" />

    @unless ($about)
        <x-field name="package_id" :label="wording('enquiry_topic')">
            <select id="package_id" name="package_id" class="input">
                <option value="">{{ wording('enquiry_general') }}</option>
                @foreach ($packages ?? [] as $option)
                    <option value="{{ $option->id }}" @selected((int) old('package_id') === $option->id)>{{ $option->name }}</option>
                @endforeach
            </select>
        </x-field>
    @endunless

    <x-field name="message" :label="wording('enquiry_message')" required class="{{ $full }}">
        <textarea id="message" name="message" rows="{{ $compact ? 4 : 5 }}" required maxlength="3000" class="input" placeholder="{{ wording('enquiry_placeholder') }}">{{ old('message') }}</textarea>
    </x-field>

    <div class="{{ $full }}">
        <button type="submit" @class(['btn btn-primary', 'w-full' => $compact, 'w-full sm:w-auto' => ! $compact]) :disabled="sending">
            <span x-show="!sending">{{ wording('enquiry_button') }}</span>
            <span x-cloak x-show="sending">{{ wording('form_sending') }}</span>
        </button>
    </div>
</form>
