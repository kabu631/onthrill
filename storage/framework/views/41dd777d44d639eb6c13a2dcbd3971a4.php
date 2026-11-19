<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['src' => null, 'path' => null, 'width' => 400, 'alt' => '', 'eager' => false, 'contain' => false]));

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

foreach (array_filter((['src' => null, 'path' => null, 'width' => 400, 'alt' => '', 'eager' => false, 'contain' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>


<?php
    $srcset = null;
    if ($path !== null) {
        ['src' => $src, 'srcset' => $srcset] = \App\Support\Thumbnails::sources($path, (int) $width);
    }
?>

<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($src): ?>
    <img src="<?php echo e($src); ?>" <?php if($srcset): ?> srcset="<?php echo e($srcset); ?>" <?php endif; ?> alt="<?php echo e($alt); ?>"
         <?php if($eager): ?> fetchpriority="high" <?php else: ?> loading="lazy" decoding="async" <?php endif; ?>
         <?php echo e($attributes->merge(['class' => 'size-full '.($contain ? 'object-contain' : 'object-cover')])); ?>>
<?php else: ?>
    <div <?php echo e($attributes->merge(['class' => 'grid size-full place-items-center bg-mist'])); ?> role="img" aria-label="<?php echo e($alt); ?>">
        
        <img src="<?php echo e(asset('favicon.svg')); ?>" alt="" loading="lazy" class="w-1/4 max-w-20 min-w-8 opacity-35">
    </div>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\desktop projects\onthrill\resources\views/components/media.blade.php ENDPATH**/ ?>