{{--
    How to pay the advance for a booking: Indian travellers (INR) by UPI or bank,
    Nepali travellers (NPR) by eSewa, Khalti or bank. Details come from
    Admin > Site Settings > Prices & Payments. Expects $booking.
--}}
@php
    $b = $booking;
    $amount = $b->advance ?? $b->total;
    $india = $b->currency === 'INR';

    if ($india) {
        $qr = media_url(setting('upi_qr'));
        $ids = array_filter(['UPI ID' => trim((string) setting('upi_id')), 'UPI number' => trim((string) setting('upi_number'))]);
        $payee = trim((string) setting('upi_name')) ?: setting('site_name');
        $bank = trim((string) setting('bank_details_india'));
        // Opens the traveller's UPI app (phones only) with payee, amount and booking reference filled in.
        $upiLink = filled(setting('upi_id')) && $amount ? 'upi://pay?'.http_build_query([
            'pa' => trim((string) setting('upi_id')), 'pn' => $payee, 'am' => number_format((float) $amount, 2, '.', ''),
            'cu' => 'INR', 'tn' => 'Booking '.$b->reference,
        ], '', '&', PHP_QUERY_RFC3986) : null;
    } else {
        $qr = media_url(setting('nepal_qr'));
        $ids = array_filter(['eSewa ID' => trim((string) setting('esewa_id')), 'Khalti ID' => trim((string) setting('khalti_id'))]);
        $payee = setting('site_name');
        $bank = trim((string) setting('bank_details_nepal'));
        $upiLink = null;
    }
    $hasDetails = $qr || $ids || $bank !== '';
@endphp

<div class="card border-saffron-300 bg-saffron-50 p-6">
    <h2 class="text-xl font-bold">{{ wording('pay_title') }}</h2>
    @if ($amount)
        <p class="mt-1 text-sm">{{ wording_html('pay_text', ['amount' => $b->money($amount), 'reference' => $b->reference]) }}</p>
    @else
        <p class="mt-1 text-sm">{{ wording_html('pay_text_request', ['reference' => $b->reference]) }}</p>
    @endif

    @if ($qr || $ids)
        <div @class(['mt-5 grid items-start gap-6', 'sm:grid-cols-[auto_1fr]' => $qr])>
            @if ($qr)
                <figure class="mx-auto w-52 rounded-lg border border-navy-200 bg-white p-3 text-center sm:mx-0">
                    <img src="{{ $qr }}" alt="{{ $india ? 'UPI' : 'eSewa / Khalti' }} QR code" width="184" height="184" class="mx-auto size-46 object-contain">
                    <figcaption class="mt-2 text-xs text-ink/60">{{ $india ? wording('pay_scan_upi') : wording('pay_scan_nepal') }}</figcaption>
                </figure>
            @endif
            <div>
                <dl class="divide-y divide-navy-200 rounded-lg border border-navy-200 bg-white text-sm">
                    @foreach ($ids as $label => $value)
                        <div class="flex items-center justify-between gap-3 px-4 py-3" x-data="{ copied: false }">
                            <div class="min-w-0">
                                <dt class="text-navy-500">{{ $label }}</dt>
                                <dd class="font-semibold break-all text-ink">{{ $value }}</dd>
                            </div>
                            <button type="button" class="btn btn-outline btn-sm shrink-0"
                                    @click="navigator.clipboard?.writeText(@js($value)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                    x-text="copied ? @js(wording('pay_copied')) : @js(wording('pay_copy'))">{{ wording('pay_copy') }}</button>
                        </div>
                    @endforeach
                    <div class="px-4 py-3">
                        <dt class="text-navy-500">{{ wording('pay_name') }}</dt>
                        <dd class="font-semibold text-ink">{{ $payee }}</dd>
                    </div>
                </dl>
                @if ($upiLink)
                    {{-- Phones only: a computer has no UPI app to open. --}}
                    <a href="{{ $upiLink }}" class="btn btn-primary mt-4 w-full sm:hidden">{{ wording('pay_upi_button', ['amount' => $b->money($amount)]) }}</a>
                @endif
            </div>
        </div>
    @endif

    @if ($bank !== '')
        <h3 class="mt-5 text-sm font-semibold text-ink">{{ wording('pay_bank') }}</h3>
        <p class="mt-1 text-sm whitespace-pre-line">{{ $bank }}</p>
    @endif

    @if (! $hasDetails)
        <p class="mt-4 text-sm text-ink">{{ wording('pay_none') }}</p>
    @elseif (filled(setting('payment_note')))
        <p class="mt-5 text-sm whitespace-pre-line text-ink">{{ setting('payment_note') }}</p>
    @endif

    @if (filled(setting('whatsapp') ?: setting('phone')))
        <a href="{{ whatsapp_url("Namaste, I have paid the advance for booking {$b->reference}. Sharing the payment screenshot.") }}" target="_blank" rel="noopener" class="btn btn-whatsapp mt-5"><x-op-icon name="whatsapp" /> {{ wording('pay_screenshot') }}</a>
    @endif
</div>
