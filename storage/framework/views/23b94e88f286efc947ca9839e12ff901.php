<?php
    $p = $package;
    $crumbs = [wording('packages_title') => route('packages.index')];
    if ($p->category) {
        $crumbs[$p->category->name] = $p->category->url();
    }
    $crumbs[$p->name] = null;

    // The package's own photos, then those of the places it visits (each with its credit).
    $photos = collect([$p->image, ...($p->gallery ?? [])])->filter()
        ->map(fn ($path) => ['path' => $path, 'alt' => $p->name, 'destination' => $p->destinations->firstWhere('image', $path)])
        ->concat($p->destinations->filter(fn ($d) => filled($d->image))->map(fn ($d) => ['path' => $d->image, 'alt' => $d->name, 'destination' => $d]))
        ->unique('path')->take(8)->values()
        ->map(fn ($photo) => [
            'path' => $photo['path'],
            'alt' => $photo['destination']?->name ?? $photo['alt'],
            'credit' => $photo['destination']?->image_credit,
            'credit_url' => $photo['destination']?->image_credit_url,
        ])->all();

    $faqs = array_values(array_filter((array) $p->faqs, fn ($f) => filled($f['question'] ?? null)));
    $facts = array_filter([
        ['icon' => 'clock', 'label' => wording('pkg_fact_duration'), 'value' => $p->durationLabel()],
        $p->start_point ? ['icon' => 'map-pin', 'label' => wording('pkg_fact_route'), 'value' => $p->start_point === $p->end_point ? $p->start_point : $p->start_point.' / '.$p->end_point] : null,
        $p->best_season ? ['icon' => 'sun', 'label' => wording('pkg_fact_season'), 'value' => $p->best_season] : null,
        $p->group_size ? ['icon' => 'users', 'label' => wording('pkg_fact_group'), 'value' => $p->group_size] : null,
        $p->difficulty ? ['icon' => 'check-circle', 'label' => wording('pkg_fact_difficulty'), 'value' => $p->difficulty] : null,
        $p->max_altitude_m ? ['icon' => 'mountain', 'label' => wording('pkg_fact_altitude'), 'value' => number_format($p->max_altitude_m).' m'] : null,
    ]);
    $placeNames = $p->destinations->pluck('name');
    $advance = (float) setting('advance_percent');
    $canBook = Gate::allows('book');
    $nationality = old('nationality', $currency === 'NPR' ? 'nepali' : 'indian');
    $currencyName = fn (string $code) => wording($code === 'NPR' ? 'currency_npr_name' : 'currency_inr_name');
    $bookLabel = $p->isOnRequest() ? wording('price_request_button') : wording('price_book_button');

    // Section tabs: only the sections this package has.
    $tabs = array_filter([
        'overview' => wording('pkg_tab_overview'),
        'itinerary' => $p->itinerary ? wording('pkg_tab_itinerary') : null,
        'included' => ($p->inclusions || $p->exclusions) ? wording('pkg_tab_included') : null,
        'before-you-go' => $p->notes ? wording('pkg_tab_before') : null,
        'places' => $p->destinations->isNotEmpty() ? wording('pkg_tab_places') : null,
        'faqs' => $faqs ? wording('pkg_tab_faqs') : null,
        'book' => $canBook ? wording('pkg_tab_book') : null,
    ]);
    $priceLine = $p->isOnRequest() ? null : money($p->price($currency), $currency);
    $heading = 'text-2xl font-bold sm:text-3xl';
?>

<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['title' => $p->meta_title ?: $p->name,'description' => $p->meta_description ?: $p->summary,'image' => media_url($p->image)]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($p->meta_title ?: $p->name),'description' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($p->meta_description ?: $p->summary),'image' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(media_url($p->image))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php $__env->startPush('schema'); ?>
        <script type="application/ld+json"><?php echo \App\Support\Seo::json(\App\Support\Seo::breadcrumbs($crumbs)); ?></script>
        <script type="application/ld+json"><?php echo \App\Support\Seo::json(\App\Support\Seo::package($p)); ?></script>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($faqs): ?>
            <script type="application/ld+json"><?php echo \App\Support\Seo::json(\App\Support\Seo::faq($faqs)); ?></script>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php $__env->stopPush(); ?>
    <?php $__env->startPush('head'); ?>
        
        <style>@media (max-width: 1023px) { :root { --bottom-bar: 4.25rem; } body { padding-bottom: var(--bottom-bar); } }</style>
    <?php $__env->stopPush(); ?>

    
    <section class="border-b border-navy-100 bg-mist">
        <div class="container-page py-8 sm:py-10">
            <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-ink/60 sm:text-sm">
                <a href="<?php echo e(route('home')); ?>" class="hover:text-saffron-700"><?php echo e(wording('breadcrumb_home')); ?></a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $crumbs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'chevron-right','class' => 'size-3 text-ink/35']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-right','class' => 'size-3 text-ink/35']); ?>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($url): ?>
                        <a href="<?php echo e($url); ?>" class="hover:text-saffron-700"><?php echo e($label); ?></a>
                    <?php else: ?>
                        <span class="text-ink/85" aria-current="page"><?php echo e($label); ?></span>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </nav>

            <div class="mt-5 flex flex-wrap items-end justify-between gap-6">
                <div class="max-w-3xl">
                    <h1 class="anim-rise text-3xl leading-tight font-bold sm:text-4xl lg:text-[2.75rem]"><?php echo e($p->name); ?></h1>
                    <ul style="--d: 80ms" class="anim-rise mt-4 flex flex-wrap gap-x-6 gap-y-2 text-sm text-ink/75">
                        <li class="flex items-center gap-1.5"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'clock','class' => 'size-4 text-saffron-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'clock','class' => 'size-4 text-saffron-600']); ?>
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
<?php endif; ?> <?php echo e($p->durationLabel()); ?></li>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($placeNames->isNotEmpty()): ?>
                            <li class="flex items-center gap-1.5"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
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
<?php endif; ?> <?php echo e($placeNames->take(4)->implode(', ')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($placeNames->count() > 4): ?> +<?php echo e($placeNames->count() - 4); ?> <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->category): ?>
                            <li><a href="<?php echo e($p->category->url()); ?>" class="flex items-center gap-1.5 hover:text-saffron-700"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'map','class' => 'size-4 text-saffron-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'map','class' => 'size-4 text-saffron-600']); ?>
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
<?php endif; ?> <?php echo e($p->category->name); ?></a></li>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </ul>
                </div>
                <div style="--d: 120ms" class="anim-rise hidden text-right lg:block">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($priceLine): ?>
                        <p class="text-sm text-ink/60"><?php echo e(wording('price_from')); ?></p>
                        <p class="font-display text-4xl leading-none font-bold text-navy-900"><?php echo e($priceLine); ?></p>
                        <p class="mt-1 text-sm text-ink/60"><?php echo e(wording('price_per_person')); ?></p>
                    <?php else: ?>
                        <p class="font-display text-2xl font-semibold text-navy-900"><?php echo e(wording('price_on_request')); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($photos): ?>
        <div data-reveal class="container-page mt-8">
            <?php echo $__env->make('partials.photo-grid', ['photos' => $photos], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <nav x-data="sectionNav" aria-label="<?php echo e($p->name); ?>" class="sticky top-16 z-30 mt-8 border-y border-navy-100 bg-white/95 backdrop-blur lg:top-20">
        <div class="container-page">
            <ul class="no-scrollbar -mx-4 flex gap-1 overflow-x-auto px-4 text-sm font-medium sm:mx-0 sm:px-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <li>
                        <a href="#<?php echo e($id); ?>" class="block border-b-2 px-3 py-4 whitespace-nowrap transition-colors"
                           :class="active === '<?php echo e($id); ?>' ? 'border-saffron-600 text-saffron-700' : 'border-transparent text-ink/70 hover:text-navy-900'"><?php echo e($label); ?></a>
                    </li>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container-page pt-10 pb-16 sm:pb-20">
        <div class="grid gap-12 lg:grid-cols-[1fr_22rem]">
            <article class="min-w-0 space-y-14">
                
                <section id="overview" data-reveal class="scroll-mt-40">
                    <h2 class="<?php echo e($heading); ?>"><?php echo e(wording('pkg_overview_title')); ?></h2>
                    <span class="title-rule"></span>

                    <dl class="mt-7 grid grid-cols-2 gap-x-6 gap-y-5 rounded-lg border border-navy-100 p-5 sm:grid-cols-3 sm:p-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $facts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fact): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <div class="flex items-start gap-3">
                                <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => $fact['icon'],'class' => 'mt-0.5 size-5 text-saffron-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($fact['icon']),'class' => 'mt-0.5 size-5 text-saffron-600']); ?>
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
                                <div class="min-w-0">
                                    <dt class="text-xs text-navy-500"><?php echo e($fact['label']); ?></dt>
                                    <dd class="mt-0.5 text-sm font-semibold text-navy-900"><?php echo e($fact['value']); ?></dd>
                                </div>
                            </div>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </dl>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->overview): ?>
                        <div class="rich mt-7"><?php echo clean_html($p->overview); ?></div>
                    <?php elseif($p->summary): ?>
                        <p class="mt-7 leading-relaxed"><?php echo e($p->summary); ?></p>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->highlights): ?>
                        <h3 class="mt-9 text-xl font-semibold"><?php echo e(wording('pkg_highlights_title')); ?></h3>
                        <ul class="mt-4 grid gap-x-8 gap-y-3 sm:grid-cols-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = array_slice($p->highlights, 0, 10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $highlight): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <li class="flex gap-3 text-[15px]"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'check','class' => 'mt-1 size-4 text-saffron-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','class' => 'mt-1 size-4 text-saffron-600']); ?>
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
<?php endif; ?> <?php echo e($highlight); ?></li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </ul>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </section>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->itinerary): ?>
                    <section id="itinerary" data-reveal class="scroll-mt-40" x-data="{ all: false, toggleAll() { this.all = !this.all; this.$el.querySelectorAll('details').forEach((d) => d.open = this.all); } }">
                        <div class="flex flex-wrap items-end justify-between gap-3">
                            <div>
                                <h2 class="<?php echo e($heading); ?>"><?php echo e(wording('pkg_itinerary_title')); ?></h2>
                                <span class="title-rule"></span>
                            </div>
                            <button type="button" class="text-sm font-semibold text-saffron-700 hover:text-saffron-800" @click="toggleAll()"
                                    x-text="all ? <?php echo \Illuminate\Support\Js::from(wording('pkg_close_all'))->toHtml() ?> : <?php echo \Illuminate\Support\Js::from(wording('pkg_open_all'))->toHtml() ?>"><?php echo e(wording('pkg_open_all')); ?></button>
                        </div>

                        <ol class="relative mt-7 space-y-3 before:absolute before:top-4 before:bottom-4 before:left-[1.65rem] before:w-px before:bg-navy-200 sm:before:left-[2.15rem]">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $p->itinerary; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <li class="relative">
                                    <details class="group rounded-lg border border-navy-100 bg-white open:shadow-card" <?php if($loop->first): ?> open <?php endif; ?>>
                                        <summary class="flex cursor-pointer list-none items-center gap-4 p-3 [&::-webkit-details-marker]:hidden sm:p-4">
                                            <span class="grid w-12 shrink-0 place-items-center rounded-md bg-navy-900 py-1.5 text-center text-white group-open:bg-saffron-600 sm:w-14">
                                                <span class="text-[11px] leading-none font-medium opacity-80"><?php echo e(wording('pkg_day')); ?></span>
                                                <span class="font-display text-lg leading-tight font-bold"><?php echo e(str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT)); ?></span>
                                            </span>
                                            <span class="min-w-0 flex-1 font-display text-[17px] font-semibold text-navy-900"><?php echo e($day['title'] ?? ''); ?></span>
                                            <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'chevron-down','class' => 'size-5 text-ink/45 group-open:rotate-180']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'chevron-down','class' => 'size-5 text-ink/45 group-open:rotate-180']); ?>
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
                                        </summary>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled($day['description'] ?? null)): ?>
                                            <p class="pr-5 pb-5 pl-[4.75rem] leading-relaxed sm:pl-[5.75rem]"><?php echo e($day['description']); ?></p>
                                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                                    </details>
                                </li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </ol>
                    </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->inclusions || $p->exclusions): ?>
                    <section id="included" data-reveal class="scroll-mt-40">
                        <h2 class="<?php echo e($heading); ?>"><?php echo e(wording('pkg_included_title')); ?></h2>
                        <span class="title-rule"></span>
                        <div class="mt-7 grid gap-5 sm:grid-cols-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->inclusions): ?>
                                <div class="rounded-lg border border-navy-100 p-6">
                                    <h3 class="flex items-center gap-2 text-lg font-semibold"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'check-circle','class' => 'size-5 text-green-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle','class' => 'size-5 text-green-700']); ?>
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
<?php endif; ?> <?php echo e(wording('pkg_included')); ?></h3>
                                    <ul class="mt-4 space-y-2.5 text-sm">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $p->inclusions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <li class="flex gap-2.5"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'check','class' => 'mt-0.5 size-4 shrink-0 text-green-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check','class' => 'mt-0.5 size-4 shrink-0 text-green-700']); ?>
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
<?php endif; ?> <?php echo e($item); ?></li>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </ul>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->exclusions): ?>
                                <div class="rounded-lg border border-navy-100 p-6">
                                    <h3 class="flex items-center gap-2 text-lg font-semibold"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'x','class' => 'size-5 text-red-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x','class' => 'size-5 text-red-700']); ?>
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
<?php endif; ?> <?php echo e(wording('pkg_excluded')); ?></h3>
                                    <ul class="mt-4 space-y-2.5 text-sm">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $p->exclusions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <li class="flex gap-2.5"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'x','class' => 'mt-0.5 size-4 shrink-0 text-ink/40']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'x','class' => 'mt-0.5 size-4 shrink-0 text-ink/40']); ?>
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
<?php endif; ?> <?php echo e($item); ?></li>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </ul>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->notes): ?>
                    <section id="before-you-go" data-reveal class="scroll-mt-40">
                        <h2 class="<?php echo e($heading); ?>"><?php echo e(wording('pkg_before_title')); ?></h2>
                        <span class="title-rule"></span>
                        <div class="rich mt-7 rounded-lg border-l-4 border-saffron-500 bg-saffron-50/60 p-6"><?php echo clean_html($p->notes); ?></div>
                    </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->destinations->isNotEmpty()): ?>
                    <section id="places" data-reveal class="scroll-mt-40">
                        <h2 class="<?php echo e($heading); ?>"><?php echo e(wording('pkg_places_title')); ?></h2>
                        <span class="title-rule"></span>
                        <ul class="mt-7 grid gap-4 sm:grid-cols-2">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $p->destinations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $destination): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <li>
                                    <a href="<?php echo e($destination->url()); ?>" class="group flex items-center gap-4 rounded-lg border border-navy-100 p-3 transition-colors hover:border-saffron-300">
                                        <span class="size-20 shrink-0 overflow-hidden rounded-md bg-mist"><?php if (isset($component)) { $__componentOriginal01fba5c3fd54b636a2e82ee391a61484 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal01fba5c3fd54b636a2e82ee391a61484 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.media','data' => ['path' => $destination->image,'width' => 160,'alt' => $destination->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('media'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['path' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($destination->image),'width' => 160,'alt' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($destination->name)]); ?>
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
<?php endif; ?></span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block font-display text-[17px] font-semibold text-navy-900 group-hover:text-saffron-700"><?php echo e($destination->name); ?></span>
                                            <span class="mt-0.5 block text-xs text-ink/60"><?php echo e(\Illuminate\Support\Str::before((string) $destination->region, ',')); ?>, <?php echo e(\Illuminate\Support\Str::before($destination->country, ' (')); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($destination->altitude_m): ?> · <?php echo e(number_format($destination->altitude_m)); ?> m <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?></span>
                                        </span>
                                        <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'arrow-right','class' => 'mr-2 size-4 text-saffron-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-right','class' => 'mr-2 size-4 text-saffron-600']); ?>
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
                                    </a>
                                </li>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </ul>
                    </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($faqs): ?>
                    <section id="faqs" data-reveal class="scroll-mt-40">
                        <h2 class="<?php echo e($heading); ?>"><?php echo e(wording('pkg_faq_title')); ?></h2>
                        <span class="title-rule"></span>
                        <div class="mt-7 space-y-3">
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                <details class="group rounded-lg border border-navy-100 bg-white open:shadow-card">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-4 font-display text-[17px] font-semibold text-navy-900 [&::-webkit-details-marker]:hidden">
                                        <?php echo e($faq['question']); ?>

                                        <span class="grid size-7 shrink-0 place-items-center rounded-md bg-mist text-saffron-600 group-open:bg-saffron-600 group-open:text-white"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'plus','class' => 'size-4 group-open:rotate-45']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'plus','class' => 'size-4 group-open:rotate-45']); ?>
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
<?php endif; ?></span>
                                    </summary>
                                    <p class="px-5 pb-5 leading-relaxed"><?php echo e($faq['answer'] ?? ''); ?></p>
                                </details>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                        </div>
                    </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canBook): ?>
                    <section id="book" data-reveal class="card scroll-mt-40 p-6 sm:p-8"
                             x-data="bookingEstimate({ inr: <?php echo \Illuminate\Support\Js::from($p->price('INR'))->toHtml() ?>, npr: <?php echo \Illuminate\Support\Js::from($p->price('NPR'))->toHtml() ?>, advance: <?php echo \Illuminate\Support\Js::from($advance)->toHtml() ?>, other: <?php echo \Illuminate\Support\Js::from($currency)->toHtml() ?>, adults: <?php echo \Illuminate\Support\Js::from((int) old('adults', 2))->toHtml() ?>, children: <?php echo \Illuminate\Support\Js::from((int) old('children', 0))->toHtml() ?>, nationality: <?php echo \Illuminate\Support\Js::from($nationality)->toHtml() ?> })">
                        <h2 class="<?php echo e($heading); ?>"><?php echo e($p->isOnRequest() ? wording('booking_title_request') : wording('booking_title')); ?></h2>
                        <span class="title-rule"></span>
                        <p class="mt-4 text-sm"><?php echo e(wording('booking_note')); ?></p>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($errors->booking->any()): ?>
                            <div role="alert" class="mt-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800"><?php echo e(wording('form_error')); ?></div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <form action="<?php echo e(route('bookings.store', $p)); ?>" method="post" class="mt-6 grid gap-4 sm:grid-cols-2" x-data="{ sending: false }" @submit="sending = true">
                            <?php echo csrf_field(); ?>
                            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'travel_date','id' => 'book-date','label' => wording('booking_date'),'type' => 'date','bag' => 'booking','required' => true,'min' => now()->toDateString()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'travel_date','id' => 'book-date','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('booking_date')),'type' => 'date','bag' => 'booking','required' => true,'min' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(now()->toDateString())]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                            <div class="grid grid-cols-2 gap-4">
                                <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'adults','id' => 'book-adults','label' => wording('booking_adults'),'bag' => 'booking','required' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'adults','id' => 'book-adults','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('booking_adults')),'bag' => 'booking','required' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <select id="book-adults" name="adults" class="input" x-model.number="adults">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 1; $i <= 20; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($i); ?>" <?php if((int) old('adults', 2) === $i): echo 'selected'; endif; ?>><?php echo e($i); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'children','id' => 'book-children','label' => wording('booking_children'),'bag' => 'booking']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'children','id' => 'book-children','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('booking_children')),'bag' => 'booking']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                    <select id="book-children" name="children" class="input" x-model.number="children">
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($i = 0; $i <= 10; $i++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                            <option value="<?php echo e($i); ?>" <?php if((int) old('children', 0) === $i): echo 'selected'; endif; ?>><?php echo e($i); ?></option>
                                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                    </select>
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                            </div>

                            <fieldset class="sm:col-span-2">
                                <legend class="label"><?php echo e(wording('booking_nationality')); ?> <span class="text-red-600" aria-hidden="true">*</span></legend>
                                <div class="flex flex-wrap gap-3">
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = \App\Models\Booking::NATIONALITIES; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                        <label class="flex cursor-pointer items-center gap-2 rounded-md border px-4 py-2.5 text-sm" :class="nationality === '<?php echo e($value); ?>' ? 'border-saffron-500 bg-saffron-50' : 'border-navy-200 bg-white'">
                                            <input type="radio" name="nationality" value="<?php echo e($value); ?>" x-model="nationality" <?php if($nationality === $value): echo 'checked'; endif; ?> class="size-4 accent-saffron-600"> <?php echo e($label); ?>

                                        </label>
                                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                                </div>
                                <p class="mt-1.5 text-xs text-ink/55"><?php echo e(wording('booking_nationality_note')); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['nationality', 'booking'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="field-error"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </fieldset>

                            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'name','id' => 'book-name','label' => wording('booking_name'),'bag' => 'booking','required' => true,'autocomplete' => 'name','maxlength' => '120','value' => auth()->user()?->name]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','id' => 'book-name','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('booking_name')),'bag' => 'booking','required' => true,'autocomplete' => 'name','maxlength' => '120','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()?->name)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'phone','id' => 'book-phone','label' => wording('booking_phone'),'type' => 'tel','bag' => 'booking','required' => true,'autocomplete' => 'tel','inputmode' => 'tel','maxlength' => '20','value' => auth()->user()?->phone]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'phone','id' => 'book-phone','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('booking_phone')),'type' => 'tel','bag' => 'booking','required' => true,'autocomplete' => 'tel','inputmode' => 'tel','maxlength' => '20','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()?->phone)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'email','id' => 'book-email','label' => wording('booking_email'),'type' => 'email','bag' => 'booking','required' => true,'autocomplete' => 'email','maxlength' => '160','value' => auth()->user()?->email,'hint' => wording('booking_email_hint')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'email','id' => 'book-email','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('booking_email')),'type' => 'email','bag' => 'booking','required' => true,'autocomplete' => 'email','maxlength' => '160','value' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(auth()->user()?->email),'hint' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('booking_email_hint'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'travelling_from','id' => 'book-from','label' => wording('booking_from'),'bag' => 'booking','maxlength' => '120','placeholder' => wording('booking_from_placeholder')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'travelling_from','id' => 'book-from','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('booking_from')),'bag' => 'booking','maxlength' => '120','placeholder' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('booking_from_placeholder'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
                            <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'notes','id' => 'book-notes','label' => wording('booking_notes'),'bag' => 'booking','class' => 'sm:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notes','id' => 'book-notes','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('booking_notes')),'bag' => 'booking','class' => 'sm:col-span-2']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

                                <textarea id="book-notes" name="notes" rows="3" maxlength="2000" class="input" placeholder="<?php echo e(wording('booking_notes_placeholder')); ?>"><?php echo e(old('notes')); ?></textarea>
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $attributes = $__attributesOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__attributesOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalae4c123bc9806121d87d234de2f27a3b)): ?>
<?php $component = $__componentOriginalae4c123bc9806121d87d234de2f27a3b; ?>
<?php unset($__componentOriginalae4c123bc9806121d87d234de2f27a3b); ?>
<?php endif; ?>

                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($p->isOnRequest())): ?>
                                
                                <div class="flex flex-wrap items-baseline justify-between gap-2 rounded-md border border-navy-100 bg-mist px-4 py-3 text-sm sm:col-span-2" x-show="total() > 0">
                                    <span><?php echo e(wording('booking_estimate')); ?> <span x-text="adults + children"></span> <span x-text="adults + children === 1 ? <?php echo \Illuminate\Support\Js::from(wording('booking_traveller'))->toHtml() ?> : <?php echo \Illuminate\Support\Js::from(wording('booking_travellers'))->toHtml() ?>"></span></span>
                                    <span>
                                        <strong class="font-display text-lg text-navy-900" x-text="format(total())"></strong>
                                        <template x-if="advance > 0"><span class="text-ink/60"> · <?php echo e(wording('booking_advance')); ?> <strong class="text-ink" x-text="format(Math.round(total() * advance / 100))"></strong></span></template>
                                    </span>
                                </div>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                            <div class="sm:col-span-2">
                                <button type="submit" class="btn btn-primary btn-lg w-full sm:w-auto" :disabled="sending">
                                    <span x-show="!sending"><?php echo e(wording('booking_button')); ?></span>
                                    <span x-cloak x-show="sending"><?php echo e(wording('form_sending')); ?></span>
                                </button>
                                <p class="mt-3 text-xs text-ink/55"><?php echo e(wording('booking_terms')); ?> <a href="<?php echo e(url('/terms-and-conditions')); ?>" class="underline"><?php echo e(wording('booking_terms_link')); ?></a>.</p>
                            </div>
                        </form>
                    </section>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <p class="text-xs text-ink/55"><?php echo e(wording('last_updated', ['date' => $p->updated_at?->format('d F Y')])); ?></p>
            </article>

            
            <aside class="space-y-5">
                <div data-reveal class="card overflow-hidden">
                    <div class="bg-navy-900 px-6 py-5 text-white">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($priceLine): ?>
                            <p class="text-sm text-white/70"><?php echo e(wording('price_from')); ?></p>
                            <p class="mt-0.5 flex flex-wrap items-baseline gap-x-2">
                                <span class="font-display text-3xl font-bold"><?php echo e($priceLine); ?></span>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->isOnSale($currency)): ?>
                                    <span class="text-sm text-white/55 line-through"><?php echo e(money($p->regularPrice($currency), $currency)); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                            <p class="mt-0.5 text-sm text-white/70"><?php echo e(wording('price_per_person')); ?>

                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($p->isOnSale($currency)): ?>
                                    <span class="badge ml-1 bg-saffron-600 text-white"><?php echo e(wording('price_off', ['percent' => $p->discountPercent($currency)])); ?></span>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </p>
                        <?php else: ?>
                            <p class="font-display text-2xl font-semibold"><?php echo e(wording('price_on_request')); ?></p>
                            <p class="mt-1 text-sm text-white/70"><?php echo e(wording('price_request_note')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>

                    <div class="p-6">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($p->isOnRequest())): ?>
                            <p class="text-xs text-ink/60">
                                <?php echo e(wording('price_shown_in', ['currency' => $currencyName($currency)])); ?> ·
                                <a href="<?php echo e(route('currency', $currency === 'NPR' ? 'inr' : 'npr')); ?>" rel="nofollow" class="font-medium text-saffron-700 underline underline-offset-2"><?php echo e(wording('price_switch', ['currency' => $currency === 'NPR' ? '₹ INR' : 'NPR'])); ?></a>
                            </p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($advance > 0): ?>
                                <p class="mt-3 text-sm"><?php echo e(wording('price_advance', ['percent' => rtrim(rtrim(number_format($advance, 1), '0'), '.')])); ?></p>
                            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php else: ?>
                            <p class="text-sm"><?php echo e(wording('price_request_text')); ?></p>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canBook): ?>
                            <a href="#book" class="btn btn-primary btn-lg mt-5 w-full"><?php echo e($bookLabel); ?></a>
                        <?php else: ?>
                            <div class="mt-5">
                                <?php echo $__env->make('partials.staff-notice', ['editUrl' => auth()->user()?->can('update', $p) ? \App\Filament\Resources\Packages\PackageResource::getUrl('edit', ['record' => $p]) : null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            </div>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('whatsapp') ?: setting('phone'))): ?>
                            <a href="<?php echo e(whatsapp_url(wording('whatsapp_greeting').' '.$p->name)); ?>" target="_blank" rel="noopener" class="btn btn-whatsapp mt-3 w-full"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'whatsapp','class' => 'size-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'whatsapp','class' => 'size-4']); ?>
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
<?php endif; ?> <?php echo e(wording('price_whatsapp')); ?></a>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                        <?php echo $__env->make('partials.trust-points', ['points' => setting('package_trust_points')], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>

                <div id="enquiry" data-reveal class="card scroll-mt-28 overflow-hidden lg:sticky lg:top-40">
                    <div class="border-b border-navy-100 px-6 pt-5 pb-4">
                        <h2 class="text-xl font-bold"><?php echo e(wording('pkg_enquiry_title')); ?></h2>
                        <p class="mt-1 text-sm"><?php echo e(wording('pkg_enquiry_text')); ?></p>
                    </div>
                    <div class="px-6 pb-6">
                        <?php echo $__env->make('partials.inquiry-form', ['package' => $p, 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($related->isNotEmpty()): ?>
        <section class="section bg-mist">
            <div class="container-page">
                <?php if (isset($component)) { $__componentOriginal755230460fd16c04121658d92fbf99f7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal755230460fd16c04121658d92fbf99f7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.section-heading','data' => ['align' => 'left','title' => wording('pkg_similar_title')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('section-heading'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['align' => 'left','title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('pkg_similar_title'))]); ?>
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
                <div data-reveal-group class="mt-10 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $related; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginalbb3566070790fe8fe5c4e22b7076e036 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalbb3566070790fe8fe5c4e22b7076e036 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.package-card','data' => ['package' => $item,'currency' => $currency]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('package-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['package' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($item),'currency' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($currency)]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalbb3566070790fe8fe5c4e22b7076e036)): ?>
<?php $attributes = $__attributesOriginalbb3566070790fe8fe5c4e22b7076e036; ?>
<?php unset($__attributesOriginalbb3566070790fe8fe5c4e22b7076e036); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalbb3566070790fe8fe5c4e22b7076e036)): ?>
<?php $component = $__componentOriginalbb3566070790fe8fe5c4e22b7076e036; ?>
<?php unset($__componentOriginalbb3566070790fe8fe5c4e22b7076e036); ?>
<?php endif; ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="fixed inset-x-0 bottom-0 z-30 border-t border-navy-100 bg-white/95 px-4 py-3 shadow-lift backdrop-blur lg:hidden">
        <div class="flex items-center justify-between gap-4">
            <div class="min-w-0">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($priceLine): ?>
                    <p class="text-xs text-ink/60"><?php echo e(wording('price_from')); ?> <span class="font-display text-lg font-bold text-navy-900"><?php echo e($priceLine); ?></span> <?php echo e(wording('price_per_person')); ?></p>
                <?php else: ?>
                    <p class="font-display text-base font-semibold text-navy-900"><?php echo e(wording('price_on_request')); ?></p>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <p class="truncate text-xs text-ink/55"><?php echo e($p->durationLabel()); ?></p>
            </div>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($canBook): ?>
                <a href="#book" class="btn btn-primary shrink-0"><?php echo e($p->isOnRequest() ? wording('price_request_button') : wording('mobile_book_button')); ?></a>
            <?php else: ?>
                <a href="#enquiry" class="btn btn-outline shrink-0"><?php echo e(wording('pkg_enquiry_title')); ?></a>
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
<?php /**PATH D:\desktop projects\onthrill\resources\views/packages/show.blade.php ENDPATH**/ ?>