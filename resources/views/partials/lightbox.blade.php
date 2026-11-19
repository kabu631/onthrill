{{--
    Full-screen photo viewer. Needs x-data="lightbox(items)" on a surrounding element,
    items being [{src, title, credit, creditUrl}]. Each photo's credit (required by its
    licence) is shown under it.
--}}
<div x-cloak x-show="image" x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-navy-950/95 p-4" role="dialog" aria-modal="true" aria-label="Photo viewer"
     @click.self="close()" @keydown.escape.window="close()" @keydown.arrow-right.window="next()" @keydown.arrow-left.window="prev()">
    <button type="button" @click="close()" class="absolute top-4 right-4 grid size-11 place-items-center rounded-md bg-white/10 text-white hover:bg-white/20" aria-label="Close"><x-op-icon name="x" /></button>
    <button type="button" x-show="images.length > 1" @click="prev()" class="absolute left-3 grid size-11 place-items-center rounded-md bg-white/10 text-white hover:bg-white/20 sm:left-6" aria-label="Previous photo"><x-op-icon name="chevron-left" /></button>
    <template x-if="image">
        <figure class="max-h-full max-w-5xl">
            <img :src="image.src" :alt="image.title || 'Photo'" class="max-h-[80vh] w-auto rounded-md object-contain">
            <figcaption class="mt-3 text-center text-sm text-white/80">
                <span x-show="image.title" x-text="image.title"></span>
                <span x-show="images.length > 1" class="ml-2 text-white/50" x-text="(current + 1) + ' / ' + images.length"></span>
                <a x-show="image.credit" :href="image.creditUrl" target="_blank" rel="noopener" x-text="image.credit" class="mt-1 block text-xs text-white/55 hover:text-white"></a>
            </figcaption>
        </figure>
    </template>
    <button type="button" x-show="images.length > 1" @click="next()" class="absolute right-3 grid size-11 place-items-center rounded-md bg-white/10 text-white hover:bg-white/20 sm:right-6" aria-label="Next photo"><x-op-icon name="chevron-right" /></button>
</div>
