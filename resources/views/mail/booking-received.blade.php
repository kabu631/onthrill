@php
    $b = $booking;
    // How to pay, in the traveller's currency (Admin > Site Settings > Prices & Payments).
    $payTo = $b->currency === 'NPR'
        ? array_filter(['eSewa ID' => setting('esewa_id'), 'Khalti ID' => setting('khalti_id')])
        : array_filter(['UPI ID' => setting('upi_id'), 'UPI number' => setting('upi_number')]);
    $bank = trim((string) setting($b->currency === 'NPR' ? 'bank_details_nepal' : 'bank_details_india'));
@endphp
<x-mail::message>
@if ($forAdmin)
# New booking {{ $b->reference }}

**{{ $b->name }}** · {{ $b->phone }} · {{ $b->email }}
@else
# {{ wording('email_greeting', ['name' => \Illuminate\Support\Str::of($b->name)->before(' ')]) }}

{{ wording('email_booking_intro', ['reference' => $b->reference, 'phone' => $b->phone]) }}
@endif

<x-mail::table>
| | |
|:--|:--|
| {{ wording('bookingpage_row_tour') }} | {{ $b->package_name }} |
| {{ wording('bookingpage_row_date') }} | {{ $b->travel_date?->format('d M Y') }} |
| {{ wording('bookingpage_row_travellers') }} | {{ $b->adults }} {{ \Illuminate\Support\Str::plural('adult', $b->adults) }}@if ($b->children), {{ $b->children }} {{ \Illuminate\Support\Str::plural('child', $b->children) }}@endif |
| {{ wording('bookingpage_row_nationality') }} | {{ \App\Models\Booking::NATIONALITIES[$b->nationality] ?? $b->nationality }} |
@if ($b->travelling_from)
| {{ wording('bookingpage_row_from') }} | {{ $b->travelling_from }} |
@endif
@if ($b->total !== null)
| {{ wording('bookingpage_row_total') }} | {{ $b->money($b->total) }} ({{ $b->money($b->price_per_person) }} {{ wording('price_per_person') }}) |
| {{ wording('email_row_advance') }} | {{ $b->money($b->advance) }} |
@else
| {{ wording('bookingpage_row_total') }} | {{ wording('price_on_request') }} |
@endif
</x-mail::table>

@if ($b->notes)
Notes: {{ $b->notes }}
@endif

@if ($forAdmin)
<x-mail::button :url="url('/admin/bookings/'.$b->id.'/edit')">Open in admin</x-mail::button>
@else
@if ($payTo || $bank !== '')
## {{ wording('email_pay_title') }}

{{ $b->advance !== null ? wording('pay_text', ['amount' => $b->money($b->advance), 'reference' => $b->reference]) : wording('pay_text_request', ['reference' => $b->reference]) }}

@foreach ($payTo as $label => $value)
{{ $label }}: **{{ $value }}**<br>
@endforeach
@if ($bank !== '')

{!! nl2br(e($bank)) !!}
@endif

@if (filled(setting('payment_note')))
{{ setting('payment_note') }}
@endif
@endif

@if ($b->user_id)
<x-mail::button :url="route('account.booking', $b)">{{ wording('email_view_booking') }}</x-mail::button>
@endif

@if (filled(setting('phone')))
{{ wording('email_call_us', ['phone' => setting('phone')]) }}
@endif

{{ wording('email_signoff') }}

{{ setting('site_name') }}
@endif
</x-mail::message>
