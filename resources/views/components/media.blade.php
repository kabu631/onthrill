@props(['src' => null, 'path' => null, 'width' => 400, 'alt' => '', 'eager' => false, 'contain' => false])

{{--
    An image, or a branded placeholder when the item has no photo yet.
    Pass `path` (a file on the public disk) and the `width` it is shown at, and a
    right-sized copy is served (plus a sharper one for high-density screens).
    `src` (a full URL) is still accepted for anything that is not an upload.
--}}
@php
    $srcset = null;
    if ($path !== null) {
        ['src' => $src, 'srcset' => $srcset] = \App\Support\Thumbnails::sources($path, (int) $width);
    }
@endphp

@if ($src)
    <img src="{{ $src }}" @if ($srcset) srcset="{{ $srcset }}" @endif alt="{{ $alt }}"
         @if ($eager) fetchpriority="high" @else loading="lazy" decoding="async" @endif
         {{ $attributes->merge(['class' => 'size-full '.($contain ? 'object-contain' : 'object-cover')]) }}>
@else
    <div {{ $attributes->merge(['class' => 'grid size-full place-items-center bg-mist']) }} role="img" aria-label="{{ $alt }}">
        {{-- No photo yet: the site's mountain mark, faint, on the page's sand colour --}}
        <img src="{{ asset('favicon.svg') }}" alt="" loading="lazy" class="w-1/4 max-w-20 min-w-8 opacity-35">
    </div>
@endif
