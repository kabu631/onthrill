{{--
    Main image with thumbnails on destination and package pages.
    Expects $paths (image files on the public disk) and $alt. Each image is served
    at the size it is shown: ~600 px for the main view, 80 px for thumbnails.
--}}
@php
    $square = $square ?? false;
    $slides = array_map(fn ($path) => \App\Support\Thumbnails::sources($path, 640), $paths);
    $thumbs = array_map(fn ($path) => \App\Support\Thumbnails::url($path, 160), $paths);
@endphp

<div x-data="mediaGallery(@js($slides))">
    <div class="card aspect-[4/3] overflow-hidden bg-white {{ $square ? 'sm:aspect-square' : '' }}">
        @if ($slides)
            <img src="{{ $slides[0]['src'] }}" @if ($slides[0]['srcset']) srcset="{{ $slides[0]['srcset'] }}" @endif
                 :src="images[active].src" :srcset="images[active].srcset"
                 alt="{{ $alt }}" fetchpriority="high" class="size-full {{ $square ? 'object-contain' : 'object-cover' }}">
        @else
            <x-media :alt="$alt" />
        @endif
    </div>

    @if (count($slides) > 1)
        <ul class="mt-3 flex gap-3">
            @foreach ($thumbs as $index => $url)
                <li>
                    <button type="button" class="block size-20 overflow-hidden rounded-md border-2 bg-white transition" :class="active === {{ $index }} ? 'border-saffron-500' : 'border-transparent opacity-70 hover:opacity-100'" @click="active = {{ $index }}" aria-label="Show image {{ $index + 1 }}">
                        <img src="{{ $url }}" alt="" loading="lazy" decoding="async" class="size-full object-cover">
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
</div>
