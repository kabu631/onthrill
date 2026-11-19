<?php
    $title = wording('contact_title');

    /*
     * Map: the embed link from Site Settings, otherwise a map of the office address,
     * the office coordinates, or the "map location" (Site Settings > Contact & Social).
     */
    $lat = setting('geo_latitude');
    $lng = setting('geo_longitude');
    $place = setting('address') ?: (filled($lat) && filled($lng) ? $lat.','.$lng : setting('map_location'));
    $map = setting('map_embed_url') ?: (filled($place) ? 'https://maps.google.com/maps?q='.rawurlencode($place).'&z=14&output=embed' : null);
    $directions = 'https://www.google.com/maps/search/?api=1&query='.rawurlencode($place ?: setting('site_name'));
?>

<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['title' => $title,'description' => wording('contact_intro')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('contact_intro'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if (isset($component)) { $__componentOriginalb00c7f6baf7ea834a0251f4153d10b8b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb00c7f6baf7ea834a0251f4153d10b8b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-banner','data' => ['title' => $title,'crumbs' => [$title => null],'intro' => wording('contact_intro')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([$title => null]),'intro' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('contact_intro'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalb00c7f6baf7ea834a0251f4153d10b8b)): ?>
<?php $attributes = $__attributesOriginalb00c7f6baf7ea834a0251f4153d10b8b; ?>
<?php unset($__attributesOriginalb00c7f6baf7ea834a0251f4153d10b8b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalb00c7f6baf7ea834a0251f4153d10b8b)): ?>
<?php $component = $__componentOriginalb00c7f6baf7ea834a0251f4153d10b8b; ?>
<?php unset($__componentOriginalb00c7f6baf7ea834a0251f4153d10b8b); ?>
<?php endif; ?>

    <div class="container-page section pt-8 sm:pt-10">
        
        <div data-reveal-group class="grid gap-x-8 gap-y-6 lg:grid-cols-[22rem_1fr] lg:grid-rows-[auto_1fr]">
            <div class="card p-6">
                <h2 class="text-xl font-bold"><?php echo e(setting('site_name')); ?></h2>
                <dl class="mt-4 divide-y divide-navy-100 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = [
                        ['label' => wording('contact_label_phone'), 'value' => setting('phone'), 'href' => 'tel:'.phone_digits(setting('phone'))],
                        ['label' => wording('contact_label_whatsapp'), 'value' => setting('whatsapp'), 'href' => whatsapp_url(wording('whatsapp_greeting')), 'blank' => true],
                        ['label' => wording('contact_label_email'), 'value' => setting('email'), 'href' => 'mailto:'.setting('email')],
                        ['label' => wording('contact_label_address'), 'value' => setting('address'), 'href' => null],
                        ['label' => wording('contact_label_hours'), 'value' => setting('business_hours'), 'href' => null],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if(blank($item['value'])): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?><?php continue; ?><?php endif; ?>
                        <div class="py-3.5 first:pt-0 last:pb-0">
                            <dt class="text-navy-500"><?php echo e($item['label']); ?></dt>
                            <dd class="mt-0.5 text-base font-medium break-words text-ink">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($item['href']): ?>
                                    <a href="<?php echo e($item['href']); ?>" <?php if(! empty($item['blank'])): ?> target="_blank" rel="noopener" <?php endif; ?> class="hover:text-saffron-700"><?php echo e($item['value']); ?></a>
                                <?php else: ?>
                                    <?php echo e($item['value']); ?>

                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </dd>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </dl>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('whatsapp') ?: setting('phone'))): ?>
                    <a href="<?php echo e(whatsapp_url(wording('whatsapp_greeting'))); ?>" target="_blank" rel="noopener" class="btn btn-whatsapp mt-6 w-full"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'whatsapp']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'whatsapp']); ?>
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
<?php endif; ?> <?php echo e(wording('contact_whatsapp_button')); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>

            <div class="card p-6 sm:p-8 lg:col-start-2 lg:row-span-2 lg:row-start-1">
                <h2 class="text-2xl font-bold"><?php echo e(wording('contact_form_title')); ?></h2>
                <p class="mt-1 text-sm"><?php echo e(wording('contact_required')); ?></p>
                <?php echo $__env->make('partials.inquiry-form', ['packages' => $packages], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($map): ?>
                
                <section class="card flex min-h-80 flex-col overflow-hidden lg:col-start-1 lg:row-start-2" aria-label="<?php echo e(wording('contact_map_title')); ?>">
                    <iframe src="<?php echo e($map); ?>" title="<?php echo e(wording('contact_map_title')); ?>: <?php echo e(setting('site_name')); ?>" loading="lazy" class="min-h-64 w-full flex-1 border-0" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
                    <a href="<?php echo e($directions); ?>" target="_blank" rel="noopener" class="flex items-center justify-center gap-2 border-t border-navy-100 px-4 py-3 text-sm font-semibold text-navy-800 transition-colors hover:text-saffron-700">
                        <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'map-pin','class' => 'size-4 text-saffron-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'map-pin','class' => 'size-4 text-saffron-600']); ?>
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
<?php endif; ?> <?php echo e(wording('contact_directions')); ?>

                    </a>
                </section>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $attributes = $__attributesOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__attributesOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal23a33f287873b564aaf305a1526eada4)): ?>
<?php $component = $__componentOriginal23a33f287873b564aaf305a1526eada4; ?>
<?php unset($__componentOriginal23a33f287873b564aaf305a1526eada4); ?>
<?php endif; ?>
<?php /**PATH D:\desktop projects\onthrill\resources\views/pages/contact.blade.php ENDPATH**/ ?>