@php
    $faqs = array_values(array_filter((array) setting('faqs'), fn ($f) => filled($f['question'] ?? null) && filled($f['answer'] ?? null)));
    $phone = setting('phone');
@endphp
@if ($faqs)
    @push('schema')
        <script type="application/ld+json">{!! \App\Support\Seo::json(\App\Support\Seo::faq($faqs)) !!}</script>
    @endpush

    <section class="section {{ $band['class'] ?? '' }}">
        <div class="container-page grid gap-12 lg:grid-cols-[1fr_1.6fr] lg:gap-16">
            <div>
                <x-section-heading align="left" :title="setting('faq_title')" :text="setting('faq_text')" />

                {{-- Somewhere to turn when the answer is not here --}}
                <div data-reveal class="mt-8 rounded-lg bg-navy-900 p-6 text-white/80">
                    <p class="font-display text-xl font-semibold text-white">{{ wording('home_faq_box_title') }}</p>
                    <p class="mt-1.5 text-sm">{{ wording('home_faq_box_text') }}</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <a href="{{ route('contact') }}" class="btn btn-primary btn-sm"><x-op-icon name="mail" class="size-4" /> {{ wording('home_faq_box_button') }}</a>
                        @if (filled(setting('whatsapp') ?: $phone))
                            <a href="{{ whatsapp_url(wording('whatsapp_greeting')) }}" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm"><x-op-icon name="whatsapp" class="size-4" /> WhatsApp</a>
                        @endif
                    </div>
                    @if ($phone)
                        <a href="tel:{{ phone_digits($phone) }}" class="mt-4 flex items-center gap-2 text-sm font-medium text-white hover:text-saffron-300"><x-op-icon name="phone" class="size-4 text-saffron-400" /> {{ $phone }}</a>
                    @endif
                </div>
            </div>

            {{-- Plain question-and-answer pairs: easy to read, and easy for search engines to quote. --}}
            <div data-reveal class="space-y-3">
                @foreach ($faqs as $faq)
                    <details class="group rounded-lg border border-navy-100 bg-white transition-colors open:border-navy-200 open:shadow-card" @if ($loop->first) open @endif>
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-display text-[17px] font-semibold text-navy-900 [&::-webkit-details-marker]:hidden">
                            {{ $faq['question'] }}
                            <span class="grid size-7 shrink-0 place-items-center rounded-md bg-mist text-saffron-600 transition-colors group-open:bg-saffron-600 group-open:text-white">
                                <x-op-icon name="plus" class="size-4 transition-transform group-open:rotate-45" />
                            </span>
                        </summary>
                        <p class="px-5 pb-5 leading-relaxed">{{ $faq['answer'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
@endif
