<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['title' => wording('e404_title'),'noindex' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('e404_title')),'noindex' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div data-reveal class="container-page section text-center">
        <p class="font-display text-7xl font-bold text-saffron-500">404</p>
        <h1 class="mt-4 text-3xl font-bold sm:text-4xl"><?php echo e(wording('e404_title')); ?></h1>
        <p class="mx-auto mt-3 max-w-md"><?php echo e(wording('e404_text')); ?></p>

        <form action="<?php echo e(route('search')); ?>" method="get" role="search" class="relative mx-auto mt-8 max-w-md">
            <label for="search-404" class="sr-only"><?php echo e(wording('search_title')); ?></label>
            <input id="search-404" type="search" name="q" placeholder="<?php echo e(wording('search_placeholder')); ?>" class="input py-3.5 pl-12">
            <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'search','class' => 'pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-ink/40']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'search','class' => 'pointer-events-none absolute top-1/2 left-4 size-5 -translate-y-1/2 text-ink/40']); ?>
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
        </form>

        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <a href="<?php echo e(route('home')); ?>" class="btn btn-primary"><?php echo e(wording('e404_home')); ?></a>
            <a href="<?php echo e(route('destinations.index')); ?>" class="btn btn-outline"><?php echo e(wording('menu_destinations')); ?></a>
            <a href="<?php echo e(route('packages.index')); ?>" class="btn btn-outline"><?php echo e(wording('menu_packages')); ?></a>
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
<?php /**PATH D:\desktop projects\onthrill\resources\views\errors/404.blade.php ENDPATH**/ ?>