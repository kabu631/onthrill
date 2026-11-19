{{-- Session messages (non-JS path) --}}
@foreach (['success' => 'border-green-200 bg-green-50 text-green-800', 'error' => 'border-red-200 bg-red-50 text-red-800'] as $type => $classes)
    @if (session($type))
        <div class="container-page pt-5">
            <div role="status" class="flex items-start gap-3 rounded-md border px-4 py-3 text-sm font-medium {{ $classes }}">
                <x-op-icon :name="$type === 'success' ? 'check-circle' : 'x'" class="mt-0.5" />
                <p>{{ session($type) }}</p>
            </div>
        </div>
    @endif
@endforeach
