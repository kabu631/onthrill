@if ($gallery->isNotEmpty())
    <section class="section {{ $band['class'] ?? '' }}">
        <div class="container-page">
            <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
                <x-section-heading align="left" :title="setting('home_gallery_title')" />
                <a href="{{ route('gallery') }}" class="more-link mb-1">{{ wording('home_gallery_link') }} <x-op-icon name="arrow-right" class="size-4" /></a>
            </div>

            {{-- The first photo is large; with six photos the grid is a full 3 × 3 --}}
            <ul data-reveal-group class="mt-10 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3">
                @foreach ($gallery as $image)
                    <li @class(['col-span-2 md:row-span-2' => $loop->first, 'max-md:hidden' => $loop->count === 6 && $loop->last])>
                        <a href="{{ route('gallery') }}" class="group relative block h-full overflow-hidden rounded-lg bg-mist {{ $loop->first ? 'aspect-[4/3] md:aspect-auto' : 'aspect-[4/3]' }}">
                            <x-media :path="$image->image" :width="$loop->first ? 800 : 400" :alt="$image->title ?: setting('site_name')" class="absolute inset-0" />
                            @if ($image->title)
                                <span class="absolute inset-x-0 bottom-0 bg-linear-to-t from-navy-950/80 to-transparent p-4 pt-10 text-sm font-medium text-white opacity-0 transition-opacity duration-300 group-hover:opacity-100 group-focus-visible:opacity-100">{{ $image->title }}</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif
