
<div x-data="searchPanel" x-cloak x-show="$store.search.open" class="fixed inset-0 z-[70]" role="dialog" aria-modal="true" aria-label="Search" @keydown.escape.window="close()">
    <div x-show="$store.search.open" x-transition.opacity.duration.150ms class="absolute inset-0 bg-ink/70" @click="close()"></div>

    <div x-show="$store.search.open"
         x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition duration-100 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="relative mx-auto w-full max-w-2xl px-3 pt-3 sm:px-4 sm:pt-20">
        <div class="overflow-hidden rounded-lg border-t-4 border-saffron-500 bg-white shadow-lift">
            <form action="<?php echo e(route('search')); ?>" method="get" role="search" class="flex items-center gap-3 border-b border-navy-100 px-4 sm:px-5">
                <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'search','class' => 'size-5 shrink-0 text-saffron-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','class' => 'size-5 shrink-0 text-saffron-600']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1effc256f38910ae5c52d1af950df88b)): ?>
<?php $attributes = $__attributesOriginal1effc256f38910ae5c52d1af950df88b; ?>
<?php unset($__attributesOriginal1effc256f38910ae5c52d1af950df88b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1effc256f38910ae5c52d1af950df88b)): ?>
<?php $component = $__componentOriginal1effc256f38910ae5c52d1af950df88b; ?>
<?php unset($__componentOriginal1effc256f38910ae5c52d1af950df88b); ?>
<?php endif; ?>
                <label for="search-panel-input" class="sr-only"><?php echo e(wording('search_title')); ?></label>
                <input id="search-panel-input" x-ref="input" x-model="term" @input.debounce.250ms="lookup()" type="search" name="q" autocomplete="off" maxlength="100"
                       placeholder="<?php echo e(wording('search_placeholder')); ?>"
                       class="h-14 min-w-0 flex-1 border-0 bg-transparent text-base text-ink placeholder:text-ink/40 focus:ring-0 focus:outline-none sm:h-16 sm:text-lg">
                <button type="button" @click="close()" class="shrink-0 rounded-md px-2 py-1 text-xs font-medium text-ink/55 hover:bg-navy-50 hover:text-ink">
                    <span class="hidden sm:inline">Esc</span><span class="sm:hidden">Close</span>
                </button>
            </form>

            <div class="max-h-[65vh] overflow-y-auto">
                
                <div x-show="term.trim().length < 2" class="p-4 sm:p-5">
                    <p class="text-sm font-semibold text-navy-900"><?php echo e(wording('search_packages')); ?></p>
                    <ul class="mt-2.5 flex flex-wrap gap-2 text-sm">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menu['tour_types']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <li><a href="<?php echo e($item['url']); ?>" class="block rounded-md border border-navy-200 px-3 py-1.5 text-ink/80 hover:border-saffron-500 hover:text-saffron-700"><?php echo e($item['name']); ?></a></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $menu['countries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <p class="mt-5 text-sm font-semibold text-navy-900"><?php echo e($country['name']); ?></p>
                        <ul class="mt-2.5 flex flex-wrap gap-2 text-sm">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $country['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <li><a href="<?php echo e($item['url']); ?>" class="block rounded-md border border-navy-200 px-3 py-1.5 text-ink/80 hover:border-saffron-500 hover:text-saffron-700"><?php echo e($item['name']); ?></a></li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </ul>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>

                
                <div x-show="term.trim().length >= 2">
                    <p x-show="loading && ! results.length" class="px-5 py-6 text-sm text-ink/60"><?php echo e(wording('search_searching')); ?></p>

                    <ul x-show="results.length" class="divide-y divide-navy-100">
                        <template x-for="result in results" :key="result.url">
                            <li>
                                <a :href="result.url" class="flex items-center gap-4 px-4 py-3 hover:bg-saffron-50 sm:px-5">
                                    <span class="grid size-12 shrink-0 place-items-center overflow-hidden rounded-md bg-mist">
                                        <img x-show="result.image" :src="result.image" alt="" class="size-full object-cover">
                                        <img x-show="! result.image" src="<?php echo e(asset('favicon.svg')); ?>" alt="" class="w-1/2 opacity-35">
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block truncate font-medium text-ink" x-text="result.name"></span>
                                        <span class="block truncate text-xs text-navy-500" x-text="result.meta"></span>
                                    </span>
                                    <span class="shrink-0 text-sm font-semibold text-navy-800" x-text="result.price"></span>
                                </a>
                            </li>
                        </template>
                    </ul>

                    <div x-show="searched && ! loading && ! results.length" class="px-5 py-6 text-sm">
                        <p class="font-medium text-ink" x-text="<?php echo \Illuminate\Support\Js::from(wording('search_none_title'))->toHtml() ?>.replace(':term', term.trim())"></p>
                        <p class="mt-1 text-ink/70"><?php echo e(wording('search_none_text')); ?> <a href="<?php echo e(route('contact')); ?>" class="font-medium text-saffron-700 underline underline-offset-2"><?php echo e(wording('list_empty_button')); ?></a></p>
                    </div>

                    <a x-show="results.length" :href="allUrl" class="sticky bottom-0 block border-t border-navy-200 bg-mist px-5 py-3 text-sm font-semibold text-saffron-700 hover:text-saffron-800">
                        <span x-text="<?php echo \Illuminate\Support\Js::from(wording('search_all_results'))->toHtml() ?>.replace(':term', term.trim())"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\desktop projects\onthrill\resources\views/partials/search-panel.blade.php ENDPATH**/ ?>