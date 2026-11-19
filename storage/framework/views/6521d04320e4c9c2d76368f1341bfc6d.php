<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['light' => false]));

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

foreach (array_filter((['light' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $logo = media_url(setting($light ? 'logo_footer' : 'logo'));
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo): ?>
    <img src="<?php echo e($logo); ?>" alt="<?php echo e(setting('site_name')); ?>" <?php echo e($attributes->merge(['class' => 'h-9 w-auto lg:h-10'])); ?>>
<?php else: ?>
    <span <?php echo e($attributes->merge(['class' => 'flex items-center gap-2.5'])); ?>>
        <svg class="size-9 shrink-0 lg:size-10" viewBox="0 0 64 64" aria-hidden="true">
            <rect width="64" height="64" rx="12" fill="<?php echo e($light ? '#ff8335' : '#13293a'); ?>"/>
            <path d="M14 46 28 22l7 11 5-7 10 20z" fill="<?php echo e($light ? '#13293a' : '#ff8335'); ?>"/>
            <circle cx="44" cy="18" r="4" fill="<?php echo e($light ? '#fff6ef' : '#ffd2b5'); ?>"/>
        </svg>
        <span class="leading-none">
            <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['block font-display text-2xl font-bold tracking-tight lg:text-[1.7rem]', 'text-white' => $light, 'text-navy-900' => ! $light]); ?>"><?php echo e(setting('site_name')); ?></span>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('tagline'))): ?>
                <span class="<?php echo \Illuminate\Support\Arr::toCssClasses(['mt-1 block text-[11px] font-medium tracking-wide', 'text-saffron-300' => $light, 'text-saffron-700' => ! $light]); ?>"><?php echo e(setting('tagline')); ?></span>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </span>
    </span>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\desktop projects\onthrill\resources\views/components/brand.blade.php ENDPATH**/ ?>