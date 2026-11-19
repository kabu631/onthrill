{{--
    Photos of a destination or package: one large and up to four smaller, opening
    in the lightbox. Expects $photos: [['path', 'alt', 'credit', 'credit_url'], ...].
--}}
@php
    $photos = array_values($photos);
    $count = count($photos);
    $shown = array_slice($photos, 0, 5);
    // Grid placement by number of photos (large screens).
    $layout = match (min($count, 5)) {
        1 => ['sm:col-span-4 sm:row-span-2'],
        2 => ['sm:col-span-2 sm:row-span-2', 'sm:col-span-2 sm:row-span-2'],
        3 => ['sm:col-span-2 sm:row-span-2', 'sm:col-span-2', 'sm:col-span-2'],
        4 => ['sm:col-span-2 sm:row-span-2', 'sm:col-span-2', '', ''],
        default => ['sm:col-span-2 sm:row-span-2', '', '', '', ''],
    };
    $items = collect($photos)->map(fn ($p) => [
        'src' => \App\Support\Thumbnails::url($p['path'], 1200),
        'title' => $p['alt'] ?? null,
        'credit' => $p['credit'] ?? null,
        'creditUrl' => $p['credit_url'] ?? null,
    ])->all();
@endphp

@if ($count > 0)
    <div x-data="lightbox(@js($items))">
        <div class="grid gap-2 sm:h-[24rem] sm:grid-cols-4 sm:grid-rows-2 lg:h-[30rem]">
            @foreach ($shown as $i => $photo)
                <button type="button" @click="show({{ $i }})" aria-label="Open photo {{ $i + 1 }} of {{ $count }}"
                        @class(['group relative block overflow-hidden rounded-lg bg-mist', 'aspect-[4/3] sm:aspect-auto' => $i === 0, 'hidden sm:block' => $i > 0, $layout[$i] ?? ''])>
                    <x-media :path="$photo['path']" :width="$i === 0 ? 800 : 400" :alt="$photo['alt'] ?? ''" :eager="$i === 0" class="absolute inset-0" />
                    @if ($i === 4 && $count > 5)
                        <span class="absolute inset-0 grid place-items-center bg-navy-950/60 font-display text-xl font-semibold text-white">{{ wording('photos_more', ['count' => $count - 5]) }}</span>
                    @endif
                    @if ($i === 0 && $count > 1)
                        <span class="absolute right-3 bottom-3 flex items-center gap-1.5 rounded-md bg-white/95 px-3 py-1.5 text-xs font-semibold text-navy-900 sm:hidden">
                            <x-op-icon name="photo" class="size-4" /> {{ wording('photos_count', ['count' => $count]) }}
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
        @if (filled($photos[0]['credit'] ?? null))
            <p class="mt-2 text-right text-[11px] text-ink/50">
                @if (filled($photos[0]['credit_url'] ?? null))<a href="{{ $photos[0]['credit_url'] }}" target="_blank" rel="noopener" class="hover:text-ink">{{ $photos[0]['credit'] }}</a>@else{{ $photos[0]['credit'] }}@endif
                · <a href="{{ url('/photo-credits') }}" class="underline underline-offset-2 hover:text-ink">{{ wording('photo_credits_link') }}</a>
            </p>
        @endif

        @include('partials.lightbox')
    </div>
@endif
