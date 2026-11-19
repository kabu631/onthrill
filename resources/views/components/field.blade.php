@props(['name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'hint' => null, 'bag' => 'default', 'id' => null])

{{--
    Labelled input with validation error. Pass a slot to render a custom control (select, textarea).
    $bag picks the error messages of one form when a page has two (booking + enquiry);
    $id keeps the two forms' fields apart.
--}}
@php
    $id ??= $name;
@endphp
<div {{ $attributes->only('class') }}>
    <label for="{{ $id }}" class="label">
        {{ $label }}
        @if ($required)<span class="text-red-600" aria-hidden="true">*</span>@endif
    </label>

    @if ($slot->isNotEmpty())
        {{ $slot }}
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}"
               @if ($type !== 'password') value="{{ old($name, $value) }}" @endif
               @required($required)
               @error($name, $bag) aria-invalid="true" aria-describedby="{{ $id }}-error" @enderror
               {{ $attributes->except('class')->merge(['class' => 'input']) }}>
    @endif

    @if ($hint)
        <p class="mt-1.5 text-xs text-ink/55">{{ $hint }}</p>
    @endif
    @error($name, $bag)
        <p id="{{ $id }}-error" class="field-error">{{ $message }}</p>
    @enderror
</div>
