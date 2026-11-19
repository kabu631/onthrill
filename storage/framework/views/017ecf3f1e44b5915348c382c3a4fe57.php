<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['destination', 'size' => 400]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['destination', 'size' => 400]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $d = $destination;
    $region = \Illuminate\Support\Str::before((string) $d->region, ',');
    $tours = $d->packages_count ?? null;
?>

<a href="<?php echo e($d->url()); ?>" <?php echo e($attributes->class(['group relative isolate block aspect-[4/5] overflow-hidden rounded-lg bg-navy-900'])); ?>>
    <?php if (isset($component)) { $__componentOriginal01fba5c3fd54b636a2e82ee391a61484 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal01fba5c3fd54b636a2e82ee391a61484 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.media','data' => ['path' => $d->image,'width' => $size,'alt' => $d->name,'class' => 'absolute inset-0 -z-20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('media'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($d->image),'width' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($size),'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($d->name),'class' => 'absolute inset-0 -z-20']); ?>
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
    
    <span class="absolute inset-0 -z-10 bg-linear-to-t from-navy-950/90 via-navy-950/25 to-transparent" aria-hidden="true"></span>

    <span class="badge absolute top-3 left-3 bg-white/95 text-navy-900"><?php echo e(\Illuminate\Support\Str::before($d->country, ' (')); ?></span>

    <span class="absolute inset-x-0 bottom-0 block p-5">
        <span class="block font-display text-xl leading-snug font-semibold text-white sm:text-2xl"><?php echo e($d->name); ?></span>
        <span class="mt-1 block text-sm text-white/80"><?php echo e($region); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($d->altitude_m): ?> · <?php echo e(number_format($d->altitude_m)); ?> m <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
        <span class="mt-3 flex items-center gap-1.5 text-sm font-semibold text-saffron-300">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($tours): ?>
                <?php echo e(wording($tours === 1 ? 'card_tours_one' : 'card_tours_many', ['count' => $tours])); ?>

            <?php else: ?>
                <?php echo e(wording('card_about')); ?>

            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'arrow-right','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'size-4']); ?>
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
        </span>
    </span>
</a>
<?php /**PATH D:\desktop projects\onthrill\resources\views/components/destination-card.blade.php ENDPATH**/ ?>