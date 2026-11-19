{{-- Call-to-action over a full-width photograph, just above the footer. --}}
@php
    $photo = setting('cta_image');
@endphp
<section class="relative isolate overflow-hidden bg-navy-900 py-20 text-white sm:py-28">
    @if ($photo)
        <x-media :path="$photo" :width="1200" alt="" class="absolute inset-0 -z-20" />
        <div class="absolute inset-0 -z-10 bg-navy-950/65" aria-hidden="true"></div>
    @endif

    <div data-reveal class="container-page text-center">
        <h2 class="mx-auto max-w-3xl text-3xl leading-tight font-bold text-white sm:text-5xl">{{ setting('cta_title') }}</h2>
        @if (filled(setting('cta_text')))
            <p class="mx-auto mt-5 max-w-xl text-white/85 sm:text-lg">{{ setting('cta_text') }}</p>
        @endif

        <div class="mt-9 flex flex-wrap justify-center gap-3">
            <a href="{{ route('contact') }}" class="btn btn-primary btn-lg"><x-op-icon name="mail" /> {{ wording('home_cta_button') }}</a>
            @if (filled(setting('phone')))
                <a href="tel:{{ phone_digits(setting('phone')) }}" class="btn btn-light btn-lg"><x-op-icon name="phone" /> {{ setting('phone') }}</a>
            @endif
            @if (filled(setting('whatsapp') ?: setting('phone')))
                <a href="{{ whatsapp_url(wording('whatsapp_greeting')) }}" target="_blank" rel="noopener" class="btn btn-whatsapp btn-lg"><x-op-icon name="whatsapp" /> WhatsApp</a>
            @endif
        </div>
    </div>
</section>
