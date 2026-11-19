@php
    // The lightbox shows a large (1200 px) copy rather than the original upload.
    // Each photo's credit (required by its licence) is shown under it in the lightbox.
    $items = $images->map(fn ($image) => ['src' => \App\Support\Thumbnails::url($image->image, 1200), 'title' => $image->title, 'credit' => $image->credit, 'creditUrl' => $image->credit_url])->values();
@endphp

<x-layout :title="wording('gallery_title')" :description="wording('gallery_intro')">
    <x-page-banner :title="wording('gallery_title')" :crumbs="[wording('gallery_title') => null]" :intro="wording('gallery_intro')" />

    <div class="container-page section pt-8 sm:pt-10" x-data="lightbox(@js($items))">
        @if ($images->isEmpty())
            <p data-reveal class="card p-10 text-center">{{ wording('gallery_empty') }}</p>
        @else
            <ul data-reveal-group class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($images as $index => $image)
                    <li>
                        <button type="button" @click="show({{ $index }})" class="group block aspect-square w-full overflow-hidden rounded-lg bg-mist" aria-label="Open photo {{ $index + 1 }}">
                            <x-media :path="$image->image" :width="400" :alt="$image->title ?: setting('site_name')" />
                        </button>
                    </li>
                @endforeach
            </ul>

            {{ $images->links() }}
        @endif

        @include('partials.lightbox')
    </div>
</x-layout>
