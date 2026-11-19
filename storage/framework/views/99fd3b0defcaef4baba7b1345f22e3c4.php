<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title', 'text' => null, 'align' => 'center', 'light' => false]));

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

foreach (array_filter((['title', 'text' => null, 'align' => 'center', 'light' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<div data-reveal <?php echo e($attributes->class(['max-w-2xl', 'mx-auto text-center' => $align === 'center'])); ?>>
    <h2 class="<?php echo \Illuminate\Support\Arr::toCssClasses(['text-3xl leading-tight font-bold sm:text-4xl', 'text-white' => $light]); ?>"><?php echo e($title); ?></h2>
    <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['title-rule', 'mx-auto' => $align === 'center']); ?>"></span>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($text): ?>
        <p class="<?php echo \Illuminate\Support\Arr::toCssClasses(['mt-4 leading-relaxed', 'text-white/80' => $light]); ?>"><?php echo e($text); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH D:\desktop projects\onthrill\resources\views/components/section-heading.blade.php ENDPATH**/ ?>