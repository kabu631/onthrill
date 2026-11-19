{{--
    Reviews from Admin > Website > Testimonials (only those switched on).
    One review is shown as a single centred quote; several become a slider.
--}}
@if ($testimonials->isNotEmpty())
    <section class="section {{ $band['class'] ?? '' }}">
        @if ($testimonials->count() === 1)
            @php($testimonial = $testimonials->first())
            <div class="container-page">
                <x-section-heading :title="setting('testimonials_title')" />

                <figure data-reveal class="mx-auto mt-10 max-w-3xl text-center">
                    <div class="flex justify-center gap-1 text-saffron-500" role="img" aria-label="{{ $testimonial->rating }} out of 5 stars">
                        @for ($i = 1; $i <= 5; $i++)
                            <x-op-icon name="star" @class(['size-5', 'text-ink/15' => $i > $testimonial->rating]) />
                        @endfor
                    </div>
                    <blockquote class="mt-6 font-display text-xl leading-relaxed text-navy-900 sm:text-2xl">“{{ $testimonial->content }}”</blockquote>
                    <figcaption class="mt-8 flex items-center justify-center gap-3">
                        <span class="grid size-12 shrink-0 place-items-center overflow-hidden rounded-full bg-saffron-100 font-display text-lg font-bold text-saffron-700">
                            @if ($photo = \App\Support\Thumbnails::url($testimonial->photo, 96))
                                <img src="{{ $photo }}" alt="" loading="lazy" class="size-full object-cover">
                            @else
                                {{ mb_substr($testimonial->name, 0, 1) }}
                            @endif
                        </span>
                        <span class="text-left">
                            <cite class="block font-semibold text-ink not-italic">{{ $testimonial->name }}</cite>
                            @if ($testimonial->location)
                                <span class="text-sm text-ink/60">{{ $testimonial->location }}</span>
                            @endif
                        </span>
                    </figcaption>
                </figure>
            </div>
        @else
            <div class="container-page" x-data="carousel">
                <div class="flex flex-wrap items-end justify-between gap-4">
                    <x-section-heading align="left" :title="setting('testimonials_title')" />
                    @if ($testimonials->count() > 3)
                        <div class="flex gap-2">
                            <button type="button" class="grid size-11 place-items-center rounded-md border border-navy-300 bg-white text-navy-800 transition hover:bg-navy-800 hover:text-white disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-navy-800" @click="move(-1)" :disabled="atStart" aria-label="Previous testimonials"><x-op-icon name="chevron-left" /></button>
                            <button type="button" class="grid size-11 place-items-center rounded-md border border-navy-300 bg-white text-navy-800 transition hover:bg-navy-800 hover:text-white disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-navy-800" @click="move(1)" :disabled="atEnd" aria-label="Next testimonials"><x-op-icon name="chevron-right" /></button>
                        </div>
                    @endif
                </div>

                <ul data-reveal x-ref="track" class="no-scrollbar -mx-4 mt-10 flex snap-x snap-mandatory gap-6 overflow-x-auto px-4 pb-4 sm:mx-0 sm:px-0" tabindex="0" aria-label="Testimonials">
                    @foreach ($testimonials as $testimonial)
                        <li class="card flex w-[85%] shrink-0 snap-start flex-col p-6 sm:w-[calc(50%-0.75rem)] lg:w-[calc(33.333%-1rem)]">
                            <div class="flex gap-0.5 text-saffron-500" role="img" aria-label="{{ $testimonial->rating }} out of 5 stars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <x-op-icon name="star" @class(['size-4', 'text-ink/15' => $i > $testimonial->rating]) />
                                @endfor
                            </div>
                            <blockquote class="mt-4 flex-1 leading-relaxed">“{{ $testimonial->content }}”</blockquote>
                            <footer class="mt-5 flex items-center gap-3 border-t border-ink/5 pt-4">
                                <span class="grid size-11 shrink-0 place-items-center overflow-hidden rounded-full bg-saffron-100 font-display text-lg font-bold text-saffron-700">
                                    @if ($photo = \App\Support\Thumbnails::url($testimonial->photo, 88))
                                        <img src="{{ $photo }}" alt="" loading="lazy" class="size-full object-cover">
                                    @else
                                        {{ mb_substr($testimonial->name, 0, 1) }}
                                    @endif
                                </span>
                                <span>
                                    <cite class="block font-semibold text-ink not-italic">{{ $testimonial->name }}</cite>
                                    @if ($testimonial->location)
                                        <span class="text-xs text-ink/55">{{ $testimonial->location }}</span>
                                    @endif
                                </span>
                            </footer>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </section>
@endif
