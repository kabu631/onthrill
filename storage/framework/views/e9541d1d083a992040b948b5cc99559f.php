<?php
    $allTitle = wording('destinations_page_title');
    $countryName = $country ? \Illuminate\Support\Str::before($country, ' (') : null;
    $title = $countryName ? wording('country_page_title', ['country' => $countryName]) : $allTitle;
    $crumbs = $country ? [$allTitle => route('destinations.index'), $countryName => null] : [$allTitle => null];
    // Country intros: Admin > Page Content > Destinations > Country pages
    $intro = $country ? wording('country_intro_'.\App\Support\Menu::countrySlug($country)) : wording('destinations_page_intro');
    $places = fn (int $count) => wording($count === 1 ? 'menu_places_one' : 'menu_places_many', ['count' => $count]);
    $all = collect($groups)->flatten(1);
    $tab = 'rounded-md border px-5 py-2.5 text-sm font-medium whitespace-nowrap transition-colors';
?>

<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['title' => $title,'description' => $intro]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($intro)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if (isset($component)) { $__componentOriginalb00c7f6baf7ea834a0251f4153d10b8b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb00c7f6baf7ea834a0251f4153d10b8b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-banner','data' => ['title' => $title,'crumbs' => $crumbs,'intro' => $intro]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($title),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($crumbs),'intro' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($intro)]); ?>
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

    <?php $__env->startPush('schema'); ?>
        <script type="application/ld+json"><?php echo \App\Support\Seo::json(\App\Support\Seo::itemList($title, $all)); ?></script>
    <?php $__env->stopPush(); ?>

    <div class="container-page section pt-10 sm:pt-14">
        <nav aria-label="Countries" class="no-scrollbar -mx-4 flex justify-start gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:justify-center sm:px-0">
            <a href="<?php echo e(route('destinations.index')); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$tab, 'border-navy-900 bg-navy-900 text-white' => ! $country, 'border-navy-200 bg-white text-navy-800 hover:border-navy-900' => $country]); ?>"><?php echo e(wording('dest_tab_all')); ?></a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Support\Menu::COUNTRY_SLUGS; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slug => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <a href="<?php echo e(route('destinations.country', $slug)); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses([$tab, 'border-navy-900 bg-navy-900 text-white' => $country === $name, 'border-navy-200 bg-white text-navy-800 hover:border-navy-900' => $country !== $name]); ?>"><?php echo e(\Illuminate\Support\Str::before($name, ' (')); ?></a>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
        </nav>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $groups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupCountry => $list): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
            <section class="mt-12 first-of-type:mt-10">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($country)): ?>
                    <div class="flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h2 class="text-2xl font-bold sm:text-3xl"><?php echo e(\Illuminate\Support\Str::before($groupCountry, ' (')); ?></h2>
                            <span class="title-rule"></span>
                        </div>
                        <a href="<?php echo e(route('destinations.country', \App\Support\Menu::countrySlug($groupCountry))); ?>" class="more-link"><?php echo e($places($list->count())); ?> <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
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
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <div data-reveal-group class="mt-7 grid grid-cols-2 gap-3 sm:gap-5 lg:grid-cols-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destination): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
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
                </div>
            </section>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            <p class="card mt-8 p-10 text-center"><?php echo e(wording('dest_empty')); ?></p>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
<?php /**PATH D:\desktop projects\onthrill\resources\views/destinations/index.blade.php ENDPATH**/ ?>