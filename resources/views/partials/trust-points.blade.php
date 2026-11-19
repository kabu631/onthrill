{{-- Short list under the booking box. Expects $points (list of strings). --}}
@php
    $points = array_slice(array_values(array_filter((array) $points)), 0, 4);
@endphp

@if ($points)
    <ul class="mt-6 grid gap-x-6 gap-y-2.5 border-t border-navy-200 pt-5 text-sm sm:grid-cols-2">
        @foreach ($points as $point)
            <li class="flex items-baseline gap-2.5"><span class="size-1.5 shrink-0 -translate-y-0.5 rotate-45 bg-saffron-500" aria-hidden="true"></span> {{ $point }}</li>
        @endforeach
    </ul>
@endif
