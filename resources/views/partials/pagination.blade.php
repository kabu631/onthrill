@if ($paginator->hasPages())
    <nav class="mt-12 flex items-center justify-center gap-1.5 text-sm font-medium" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="grid size-10 place-items-center rounded-md text-ink/25"><x-op-icon name="chevron-left" class="size-4" /></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="Previous page" class="grid size-10 place-items-center rounded-md border border-navy-200 bg-white text-navy-900 hover:border-saffron-500 hover:text-saffron-600"><x-op-icon name="chevron-left" class="size-4" /></a>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <span class="px-1 text-ink/40">{{ $element }}</span>
            @endif

            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span aria-current="page" class="grid size-10 place-items-center rounded-md bg-saffron-500 text-white">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="grid size-10 place-items-center rounded-md border border-navy-200 bg-white text-navy-900 hover:border-saffron-500 hover:text-saffron-600">{{ $page }}</a>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="Next page" class="grid size-10 place-items-center rounded-md border border-navy-200 bg-white text-navy-900 hover:border-saffron-500 hover:text-saffron-600"><x-op-icon name="chevron-right" class="size-4" /></a>
        @else
            <span class="grid size-10 place-items-center rounded-md text-ink/25"><x-op-icon name="chevron-right" class="size-4" /></span>
        @endif
    </nav>
@endif
