<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($featuredDestinations->isNotEmpty()): ?>
    <?php
        $countries = $destinations->pluck('country')->unique()->map(fn ($c) => \Illuminate\Support\Str::before($c, ' ('))->values();
    ?>
    <section class="section <?php echo e($band['class'] ?? ''); ?>">
        <div class="container-page">
            <div class="flex flex-wrap items-end justify-between gap-x-10 gap-y-4">
                <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['align' => 'left','title' => setting('destinations_title'),'text' => setting('destinations_text')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'left','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(setting('destinations_title')),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(setting('destinations_text'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $attributes = $__attributesOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__attributesOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal755230460fd16c04121658d92fbf99f7)): ?>
<?php $component = $__componentOriginal755230460fd16c04121658d92fbf99f7; ?>
<?php unset($__componentOriginal755230460fd16c04121658d92fbf99f7); ?>
<?php endif; ?>
                <a href="<?php echo e(route('destinations.index')); ?>" class="more-link mb-1"><?php echo e(wording('home_destinations_link')); ?> <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
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
<?php endif; ?></a>
            </div>

            <div data-reveal-group class="mt-10 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $featuredDestinations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destination): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginala654b68189fc78604d72908e258b1f1f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginala654b68189fc78604d72908e258b1f1f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.destination-card','data' => ['destination' => $destination]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('destination-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['destination' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($destination)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginala654b68189fc78604d72908e258b1f1f)): ?>
<?php $attributes = $__attributesOriginala654b68189fc78604d72908e258b1f1f; ?>
<?php unset($__attributesOriginala654b68189fc78604d72908e258b1f1f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginala654b68189fc78604d72908e258b1f1f)): ?>
<?php $component = $__componentOriginala654b68189fc78604d72908e258b1f1f; ?>
<?php unset($__componentOriginala654b68189fc78604d72908e258b1f1f); ?>
<?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>

                
                <a href="<?php echo e(route('destinations.index')); ?>" class="group flex aspect-[4/5] flex-col justify-between rounded-lg bg-navy-900 p-5 text-white transition-colors hover:bg-navy-800 sm:p-6">
                    <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'map','class' => 'size-9 text-saffron-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'map','class' => 'size-9 text-saffron-400']); ?>
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
                    <span>
                        <span class="block font-display text-5xl leading-none font-bold"><?php echo e($destinations->count()); ?></span>
                        <span class="mt-2 block text-sm text-white/75"><?php echo e(wording('home_destinations_tile', ['countries' => $countries->implode(', ')])); ?></span>
                        <span class="mt-5 flex items-center gap-1.5 text-sm font-semibold text-saffron-300">
                            <?php echo e(wording('home_destinations_tile_link')); ?> <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
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
            </div>
        </div>
    </section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\desktop projects\onthrill\resources\views/home/destinations.blade.php ENDPATH**/ ?>