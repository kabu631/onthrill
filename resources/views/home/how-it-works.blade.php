@php
    $steps = array_values(array_filter((array) setting('how_steps'), fn ($s) => filled($s['title'] ?? null)));
@endphp
@if ($steps)
    <section class="section {{ $band['class'] ?? '' }}">
        <div class="container-page">
            <x-section-heading :title="setting('how_title')" :text="setting('how_text')" />

            <ol data-reveal-group class="mt-14 grid gap-10 sm:grid-cols-2 lg:gap-8 {{ [1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3'][count($steps)] ?? 'lg:grid-cols-4' }}">
                @foreach ($steps as $step)
                    <li class="relative">
                        {{-- Dashed line on to the next step (large screens) --}}
                        @unless ($loop->last)
                            <span class="absolute top-7 left-20 hidden h-px w-[calc(100%-4.5rem)] border-t-2 border-dashed border-navy-200 lg:block" aria-hidden="true"></span>
                        @endunless
                        <div class="relative grid size-14 place-items-center rounded-lg bg-navy-900 text-white">
                            @if ($icon = media_url($step['icon'] ?? null))
                                <img src="{{ $icon }}" alt="" width="30" height="30" loading="lazy" class="size-7 object-contain brightness-0 invert">
                            @else
                                <span class="font-display text-xl font-bold">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            @endif
                        </div>
                        <h3 class="mt-6 text-xl font-semibold">{{ $step['title'] }}</h3>
                        <p class="mt-2 max-w-xs text-sm leading-relaxed">{{ $step['text'] ?? '' }}</p>
                    </li>
                @endforeach
            </ol>

            @if (filled(setting('how_button_label')) && filled(setting('how_button_url')))
                <div data-reveal class="mt-14 text-center">
                    <a href="{{ url(setting('how_button_url')) }}" class="btn btn-primary btn-lg">{{ setting('how_button_label') }}</a>
                </div>
            @endif
        </div>
    </section>
@endif
