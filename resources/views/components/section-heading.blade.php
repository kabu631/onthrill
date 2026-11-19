@props(['title', 'text' => null, 'align' => 'center', 'light' => false])

{{-- Section title with a short saffron bar under it. $light is for dark backgrounds. --}}
<div data-reveal {{ $attributes->class(['max-w-2xl', 'mx-auto text-center' => $align === 'center']) }}>
    <h2 @class(['text-3xl leading-tight font-bold sm:text-4xl', 'text-white' => $light])>{{ $title }}</h2>
    <span @class(['title-rule', 'mx-auto' => $align === 'center'])></span>
    @if ($text)
        <p @class(['mt-4 leading-relaxed', 'text-white/80' => $light])>{{ $text }}</p>
    @endif
</div>
