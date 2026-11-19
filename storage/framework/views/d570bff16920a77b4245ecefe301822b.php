<?php
    $items = array_values(array_filter((array) setting('why_items'), fn ($item) => filled($item['title'] ?? null)));
    $image = setting('why_image');
    $small = setting('why_image_small');
    // One icon per point, in order; the points themselves are edited in the admin.
    $icons = ['banknotes', 'shield', 'users', 'phone', 'map', 'sun'];
?>
<?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($items): ?>
    <section class="section overflow-hidden <?php echo e($band['class'] ?? ''); ?>">
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['container-page grid items-center gap-14', 'lg:grid-cols-2 lg:gap-20' => $image]); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($image): ?>
                
                <div data-reveal class="relative pr-10 pb-16 sm:pr-24 lg:pr-16">
                    <div class="aspect-[4/5] overflow-hidden rounded-lg bg-mist">
                        <?php if (isset($component)) { $__componentOriginal01fba5c3fd54b636a2e82ee391a61484 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal01fba5c3fd54b636a2e82ee391a61484 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.media','data' => ['path' => $image,'width' => 800,'alt' => '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('media'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($image),'width' => 800,'alt' => '']); ?>
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
                    </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($small): ?>
                        <div class="absolute right-0 bottom-0 aspect-square w-1/2 overflow-hidden rounded-lg border-[6px] border-white bg-mist shadow-lift">
                            <?php if (isset($component)) { $__componentOriginal01fba5c3fd54b636a2e82ee391a61484 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal01fba5c3fd54b636a2e82ee391a61484 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.media','data' => ['path' => $small,'width' => 400,'alt' => '']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('media'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($small),'width' => 400,'alt' => '']); ?>
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
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div>
                <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['align' => 'left','title' => setting('why_title'),'text' => setting('why_text')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'left','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(setting('why_title')),'text' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(setting('why_text'))]); ?>
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

                <ul data-reveal-group class="mt-10 grid gap-x-8 gap-y-8 sm:grid-cols-2">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <li>
                            <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => $icons[$loop->index] ?? 'check-circle','class' => 'size-8 text-saffron-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($icons[$loop->index] ?? 'check-circle'),'class' => 'size-8 text-saffron-600']); ?>
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
                            <h3 class="mt-3 text-lg font-semibold"><?php echo e($item['title']); ?></h3>
                            <p class="mt-1.5 text-sm leading-relaxed"><?php echo e($item['text'] ?? ''); ?></p>
                        </li>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </ul>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(wording('home_why_button'))): ?>
                    <div data-reveal class="mt-10 flex flex-wrap gap-3">
                        <a href="<?php echo e(url(wording('home_why_button_link'))); ?>" class="btn btn-dark"><?php echo e(wording('home_why_button')); ?></a>
                        <a href="<?php echo e(route('contact')); ?>" class="btn btn-outline"><?php echo e(wording('home_why_button2')); ?></a>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </section>
<?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
<?php /**PATH D:\desktop projects\onthrill\resources\views/home/why.blade.php ENDPATH**/ ?>