{{--
    Floating buttons: back to top (appears after scrolling) above the WhatsApp chat button.
    --bottom-bar lifts them above a bar fixed to the bottom of the screen (package pages on phones).
--}}
<div x-data="backToTop" class="fixed right-4 z-30 flex flex-col items-center gap-3 sm:right-6" style="bottom: calc(1.25rem + var(--bottom-bar, 0px))">
    <button type="button" x-cloak x-show="visible" @click="toTop"
            x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-y-3 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="grid size-11 place-items-center rounded-full bg-navy-800 text-white shadow-lift transition-colors hover:bg-navy-900"
            aria-label="Back to top" title="Back to top">
        <x-op-icon name="arrow-up" class="size-5" />
    </button>

    @if (filled(setting('whatsapp') ?: setting('phone')))
        <a href="{{ whatsapp_url(wording('whatsapp_greeting')) }}" target="_blank" rel="noopener"
           class="grid size-14 place-items-center rounded-full bg-whatsapp text-white shadow-lift transition-colors hover:bg-[#1ebe5b]"
           aria-label="Chat with us on WhatsApp" title="Chat on WhatsApp">
            <x-op-icon name="whatsapp" class="size-8" />
        </a>
    @endif
</div>

{{-- Short messages raised by JavaScript --}}
<div x-data class="pointer-events-none fixed inset-x-0 top-24 z-[60] flex flex-col items-center gap-2 px-4" aria-live="polite">
    <template x-for="toast in $store.toast.items" :key="toast.id">
        <div x-transition.opacity
             class="pointer-events-auto flex max-w-md items-center gap-3 rounded-md px-5 py-3 text-sm font-medium text-white shadow-lift"
             :class="toast.type === 'error' ? 'bg-red-600' : 'bg-navy-900'">
            <span x-text="toast.message"></span>
        </div>
    </template>
</div>
