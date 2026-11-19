@php
    $items = array_values(array_filter((array) setting('why_items'), fn ($item) => filled($item['title'] ?? null)));
    $image = setting('why_image');
    $small = setting('why_image_small');
    // One icon per point, in order; the points themselves are edited in the admin.
    $icons = ['banknotes', 'shield', 'users', 'phone', 'map', 'sun'];
@endphp
@if ($items)
    <section class="section overflow-hidden {{ $band['class'] ?? '' }}">
        <div @class(['container-page grid items-center gap-14', 'lg:grid-cols-2 lg:gap-20' => $image])>
            @if ($image)
                {{-- Two photographs, the smaller one overlapping the corner of the larger --}}
                <div data-reveal class="relative pr-10 pb-16 sm:pr-24 lg:pr-16">
                    <div class="aspect-[4/5] overflow-hidden rounded-lg bg-mist">
                        <x-media :path="$image" :width="800" alt="" />
                    </div>
                    @if ($small)
                        <div class="absolute right-0 bottom-0 aspect-square w-1/2 overflow-hidden rounded-lg border-[6px] border-white bg-mist shadow-lift">
                            <x-media :path="$small" :width="400" alt="" />
                        </div>
                    @endif
                </div>
            @endif

            <div>
                <x-section-heading align="left" :title="setting('why_title')" :text="setting('why_text')" />

                <ul data-reveal-group class="mt-10 grid gap-x-8 gap-y-8 sm:grid-cols-2">
                    @foreach ($items as $item)
                        <li>
                            <x-op-icon :name="$icons[$loop->index] ?? 'check-circle'" class="size-8 text-saffron-600" />
                            <h3 class="mt-3 text-lg font-semibold">{{ $item['title'] }}</h3>
                            <p class="mt-1.5 text-sm leading-relaxed">{{ $item['text'] ?? '' }}</p>
                        </li>
                    @endforeach
                </ul>

                @if (filled(wording('home_why_button')))
                    <div data-reveal class="mt-10 flex flex-wrap gap-3">
                        <a href="{{ url(wording('home_why_button_link')) }}" class="btn btn-dark">{{ wording('home_why_button') }}</a>
                        <a href="{{ route('contact') }}" class="btn btn-outline">{{ wording('home_why_button2') }}</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
