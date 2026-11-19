<?php
    $countries = $destinations->pluck('country')->unique()->map(fn ($c) => \Illuminate\Support\Str::before($c, ' ('))->values();
    $countryList = $countries->count() > 1 ? $countries->slice(0, -1)->implode(', ').' and '.$countries->last() : $countries->first();
    $facts = array_values(array_filter([
        $destinations->isNotEmpty() ? ['icon' => 'map-pin', 'value' => $destinations->count(), 'label' => wording('fact_places')] : null,
        $packageCount ? ['icon' => 'map', 'value' => $packageCount, 'label' => wording('fact_packages')] : null,
        $countries->isNotEmpty() ? ['icon' => 'globe', 'value' => $countries->count(), 'label' => wording('fact_countries', ['list' => $countryList])] : null,
        ['icon' => 'banknotes', 'value' => wording('fact_currency_value'), 'label' => wording('fact_currency')],
    ]));
?>


<section class="bg-white pt-14 pb-2 sm:pt-16" aria-label="At a glance">
    <div class="container-page">
        <dl data-reveal-group class="grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-4">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $facts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['flex flex-col items-center text-center sm:flex-row sm:items-start sm:gap-4 sm:text-left', 'lg:border-l lg:border-navy-100 lg:pl-8' => ! $loop->first]); ?>">
                    <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => $fact['icon'],'class' => 'mb-3 size-9 text-saffron-600 sm:mb-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fact['icon']),'class' => 'mb-3 size-9 text-saffron-600 sm:mb-0']); ?>
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
                    <div>
                        <dd class="font-display text-3xl leading-none font-bold text-navy-900 sm:text-4xl"><?php echo e($fact['value']); ?></dd>
                        <dt class="mt-2 text-sm leading-snug"><?php echo e($fact['label']); ?></dt>
                    </div>
                </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </dl>
    </div>
</section>
<?php /**PATH D:\desktop projects\onthrill\resources\views/home/facts.blade.php ENDPATH**/ ?>