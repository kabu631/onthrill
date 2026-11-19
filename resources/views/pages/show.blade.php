@php
    $phone = setting('phone');
    $whatsapp = setting('whatsapp') ?: $phone;
@endphp

<x-layout :title="$page->meta_title ?: $page->title" :description="$page->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $page->content), 160)">
    <x-page-banner :title="$page->title" :crumbs="[$page->title => null]" />

    <div class="container-page section pt-10 sm:pt-14">
        <div class="grid gap-12 lg:grid-cols-[1fr_21rem]">
            <div class="min-w-0">
                <article data-reveal class="rich">
                    {!! clean_html($page->content) !!}
                </article>
                <p class="mt-10 text-xs text-ink/55">{{ wording('last_updated', ['date' => $page->updated_at?->format('d F Y')]) }}</p>
            </div>

            {{-- Sidebar: the other information pages, help, and popular tours --}}
            <aside class="space-y-6">
                @if ($pages->count() > 1)
                    <nav data-reveal class="card p-5" aria-label="{{ wording('page_sidebar_pages') }}">
                        <h2 class="px-1 text-lg font-semibold">{{ wording('page_sidebar_pages') }}</h2>
                        <ul class="mt-3 space-y-0.5 text-sm">
                            @foreach ($pages as $item)
                                <li>
                                    <a href="{{ route('pages.show', $item) }}" @class(['flex items-center justify-between gap-3 rounded-md px-3 py-2.5', 'bg-saffron-50 font-semibold text-saffron-700' => $item->is($page), 'text-ink/80 hover:bg-mist hover:text-navy-900' => ! $item->is($page)]) @if ($item->is($page)) aria-current="page" @endif>
                                        {{ $item->menuLabel() }}
                                        <x-op-icon name="chevron-right" class="size-4 opacity-50" />
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endif

                <div data-reveal class="rounded-lg bg-navy-900 p-6 text-sm text-white/80">
                    <p class="font-display text-xl font-semibold text-white">{{ wording('page_sidebar_help_title') }}</p>
                    <p class="mt-2 leading-relaxed">{{ wording('page_sidebar_help_text') }}</p>
                    <a href="{{ route('contact') }}" class="btn btn-primary mt-5 w-full"><x-op-icon name="mail" class="size-4" /> {{ wording('page_sidebar_help_button') }}</a>
                    @if ($whatsapp)
                        <a href="{{ whatsapp_url(wording('whatsapp_greeting')) }}" target="_blank" rel="noopener" class="btn btn-whatsapp mt-3 w-full"><x-op-icon name="whatsapp" class="size-4" /> WhatsApp</a>
                    @endif
                    @if ($phone)
                        <a href="tel:{{ phone_digits($phone) }}" class="mt-4 flex items-center justify-center gap-2 font-medium text-white hover:text-saffron-300"><x-op-icon name="phone" class="size-4 text-saffron-400" /> {{ $phone }}</a>
                    @endif
                </div>

                @if ($tours->isNotEmpty())
                    <div data-reveal class="card p-5">
                        <h2 class="px-1 text-lg font-semibold">{{ wording('page_sidebar_tours') }}</h2>
                        <ul class="mt-4 space-y-4">
                            @foreach ($tours as $tour)
                                <li>
                                    <a href="{{ $tour->url() }}" class="group flex gap-3">
                                        <span class="size-20 shrink-0 overflow-hidden rounded-md bg-mist"><x-media :path="$tour->image" :width="160" :alt="$tour->name" /></span>
                                        <span class="min-w-0">
                                            <span class="line-clamp-2 font-display text-[15px] leading-snug font-semibold text-navy-900 group-hover:text-saffron-700">{{ $tour->name }}</span>
                                            <span class="mt-1 block text-xs text-ink/60">{{ $tour->durationLabel() }}</span>
                                            <span class="mt-1 block text-sm font-semibold text-navy-800">
                                                {{ $tour->isOnRequest() ? wording('price_on_request') : wording('price_from').' '.money($tour->price($currency), $currency) }}
                                            </span>
                                        </span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('packages.index') }}" class="more-link mt-5 px-1">{{ wording('page_sidebar_all_tours') }} <x-op-icon name="arrow-right" class="size-4" /></a>
                    </div>
                @endif
            </aside>
        </div>
    </div>
</x-layout>
