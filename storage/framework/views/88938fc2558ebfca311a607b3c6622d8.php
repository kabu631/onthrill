
<?php
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
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($count > 0): ?>
    <div x-data="lightbox(<?php echo \Illuminate\Support\Js::from($items)->toHtml() ?>)">
        <div class="grid gap-2 sm:h-[24rem] sm:grid-cols-4 sm:grid-rows-2 lg:h-[30rem]">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $shown; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <button type="button" @click="show(<?php echo e($i); ?>)" aria-label="Open photo <?php echo e($i + 1); ?> of <?php echo e($count); ?>"
                        class="<?php echo \Illuminate\Support\Arr::toCssClasses(['group relative block overflow-hidden rounded-lg bg-mist', 'aspect-[4/3] sm:aspect-auto' => $i === 0, 'hidden sm:block' => $i > 0, $layout[$i] ?? '']); ?>">
                    <?php if (isset($component)) { $__componentOriginal01fba5c3fd54b636a2e82ee391a61484 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal01fba5c3fd54b636a2e82ee391a61484 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.media','data' => ['path' => $photo['path'],'width' => $i === 0 ? 800 : 400,'alt' => $photo['alt'] ?? '','eager' => $i === 0,'class' => 'absolute inset-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('media'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($photo['path']),'width' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($i === 0 ? 800 : 400),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($photo['alt'] ?? ''),'eager' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($i === 0),'class' => 'absolute inset-0']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal01fba5c3fd54b636a2e82ee391a61484)): ?>
<?php $attributes = $__attributesOriginal01fba5c3fd54b636a2e82ee391a61484; ?>
<?php unset($__attributesOriginal01fba5c3fd54b636a2e82ee391a61484); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal01fba5c3fd54b636a2e82ee391a61484)): ?>
<?php $component = $__componentOriginal01fba5c3fd54b636a2e82ee391a61484; ?>
<?php unset($__componentOriginal01fba5c3fd54b636a2e82ee391a61484); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i === 4 && $count > 5): ?>
                        <span class="absolute inset-0 grid place-items-center bg-navy-950/60 font-display text-xl font-semibold text-white"><?php echo e(wording('photos_more', ['count' => $count - 5])); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($i === 0 && $count > 1): ?>
                        <span class="absolute right-3 bottom-3 flex items-center gap-1.5 rounded-md bg-white/95 px-3 py-1.5 text-xs font-semibold text-navy-900 sm:hidden">
                            <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'photo','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'photo','class' => 'size-4']); ?>
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
<?php endif; ?> <?php echo e(wording('photos_count', ['count' => $count])); ?>

                        </span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </button>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($photos[0]['credit'] ?? null)): ?>
            <p class="mt-2 text-right text-[11px] text-ink/50">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($photos[0]['credit_url'] ?? null)): ?><a href="<?php echo e($photos[0]['credit_url']); ?>" target="_blank" rel="noopener" class="hover:text-ink"><?php echo e($photos[0]['credit']); ?></a><?php else: ?><?php echo e($photos[0]['credit']); ?><?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                · <a href="<?php echo e(url('/photo-credits')); ?>" class="underline underline-offset-2 hover:text-ink"><?php echo e(wording('photo_credits_link')); ?></a>
            </p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php echo $__env->make('partials.lightbox', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\desktop projects\onthrill\resources\views/partials/photo-grid.blade.php ENDPATH**/ ?>