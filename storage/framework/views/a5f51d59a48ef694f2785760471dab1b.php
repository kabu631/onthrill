
<?php
    $partials = [
        'hero' => 'home.hero',
        'facts' => 'home.facts',
        'destinations' => 'home.destinations',
        'packages' => 'home.packages',
        'why' => 'home.why',
        'how' => 'home.how-it-works',
        'testimonials' => 'home.testimonials',
        'gallery' => 'home.gallery',
        'faq' => 'home.faq',
        'cta' => 'home.cta',
    ];
    $shown = (array) setting('home_sections', array_keys($partials));

    // Sections that have nothing to show are skipped by their partial too.
    $hasContent = [
        'destinations' => $featuredDestinations->isNotEmpty(),
        'packages' => $packages->isNotEmpty(),
        'why' => filled((array) setting('why_items')),
        'how' => filled((array) setting('how_steps')),
        'testimonials' => $testimonials->isNotEmpty(),
        'gallery' => $gallery->isNotEmpty(),
        'faq' => filled((array) setting('faqs')),
    ];
    $visible = array_values(array_filter(array_keys($partials), fn ($key) => in_array($key, $shown, true) && ($hasContent[$key] ?? true)));

    // The hero, the facts strip under it and the call-to-action band have their own
    // backgrounds. The other sections alternate white and light grey-blue.
    $ownBackground = ['hero', 'facts', 'cta'];
    $bands = [];
    $count = 0;
    foreach ($visible as $key) {
        if (! in_array($key, $ownBackground, true)) {
            $bands[$key] = ['class' => $count++ % 2 === 0 ? 'bg-white' : 'bg-mist'];
        }
    }
?>

<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['overHero' => in_array('hero', $visible, true)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['over-hero' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(in_array('hero', $visible, true))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startPush('schema'); ?>
        
        <script type="application/ld+json"><?php echo \App\Support\Seo::json(\App\Support\Seo::website()); ?></script>
    <?php $__env->stopPush(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $visible; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
        <?php echo $__env->make($partials[$key], ['band' => $bands[$key] ?? null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
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
<?php /**PATH D:\desktop projects\onthrill\resources\views/home.blade.php ENDPATH**/ ?>