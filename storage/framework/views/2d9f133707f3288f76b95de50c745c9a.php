<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['package', 'currency', 'size' => 'base']));

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

foreach (array_filter((['package', 'currency', 'size' => 'base']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $main = $size === 'lg' ? 'text-3xl' : 'text-lg';
?>

<div <?php echo e($attributes->merge(['class' => 'flex flex-wrap items-baseline gap-x-2 gap-y-0.5'])); ?>>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($package->isOnRequest()): ?>
        <span class="<?php echo e($size === 'lg' ? 'text-2xl' : 'text-base'); ?> font-semibold text-navy-800"><?php echo e(wording('price_on_request')); ?></span>
    <?php else: ?>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($size !== 'lg'): ?>
            <span class="text-xs text-ink/55"><?php echo e(wording('price_from')); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <span class="<?php echo e($main); ?> font-bold text-navy-800"><?php echo e(money($package->price($currency), $currency)); ?></span>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($package->isOnSale($currency)): ?>
            <span class="text-sm text-ink/45 line-through"><?php echo e(money($package->regularPrice($currency), $currency)); ?></span>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <span class="text-xs text-ink/55"><?php echo e(wording('price_per_person')); ?></span>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH D:\desktop projects\onthrill\resources\views/components/price.blade.php ENDPATH**/ ?>