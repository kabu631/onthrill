
<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['title' => null, 'description' => null, 'image' => null, 'noindex' => false, 'type' => 'website', 'overHero' => false]));

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

foreach (array_filter((['title' => null, 'description' => null, 'image' => null, 'noindex' => false, 'type' => 'website', 'overHero' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $siteName = setting('site_name');
    $pageTitle = $title ? $title.' | '.$siteName : setting('meta_title');
    $pageDescription = \Illuminate\Support\Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags((string) ($description ?: setting('meta_description'))))), 160);
    $shareImage = $image ?: media_url(setting('share_image'));
    // Canonical URL: the page without tracking or filter parameters (pagination is kept).
    $canonical = url()->current().(request()->integer('page') > 1 ? '?page='.request()->integer('page') : '');
?>

<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e($pageTitle); ?></title>
    <meta name="description" content="<?php echo e($pageDescription); ?>">
    <link rel="canonical" href="<?php echo e($canonical); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($noindex): ?>
        <meta name="robots" content="noindex, follow">
    <?php else: ?>
        <meta name="robots" content="index, follow, max-image-preview:large">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <meta property="og:type" content="<?php echo e($type); ?>">
    <meta property="og:site_name" content="<?php echo e($siteName); ?>">
    <meta property="og:locale" content="en_IN">
    <meta property="og:title" content="<?php echo e($pageTitle); ?>">
    <meta property="og:description" content="<?php echo e($pageDescription); ?>">
    <meta property="og:url" content="<?php echo e($canonical); ?>">
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($shareImage): ?>
        <meta property="og:image" content="<?php echo e($shareImage); ?>">
        <meta name="twitter:image" content="<?php echo e($shareImage); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($pageTitle); ?>">
    <meta name="twitter:description" content="<?php echo e($pageDescription); ?>">

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('geo_region'))): ?>
        <meta name="geo.region" content="<?php echo e(setting('geo_region')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('geo_placename'))): ?>
        <meta name="geo.placename" content="<?php echo e(setting('geo_placename')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('geo_latitude')) && filled(setting('geo_longitude'))): ?>
        <meta name="geo.position" content="<?php echo e(setting('geo_latitude')); ?>;<?php echo e(setting('geo_longitude')); ?>">
        <meta name="ICBM" content="<?php echo e(setting('geo_latitude')); ?>, <?php echo e(setting('geo_longitude')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('google_site_verification'))): ?>
        <meta name="google-site-verification" content="<?php echo e(setting('google_site_verification')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('bing_site_verification'))): ?>
        <meta name="msvalidate.01" content="<?php echo e(setting('bing_site_verification')); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <meta name="theme-color" content="#13293a">
    <link rel="icon" href="<?php echo e(asset('favicon.svg')); ?>" type="image/svg+xml">
    <link rel="alternate" type="text/plain" href="<?php echo e(route('llms')); ?>" title="Site summary for AI assistants">

    
    <link rel="preload" as="font" type="font/woff2" href="<?php echo e(Vite::asset('resources/fonts/poppins-400.woff2')); ?>" crossorigin>
    <link rel="preload" as="font" type="font/woff2" href="<?php echo e(Vite::asset('resources/fonts/outfit-600.woff2')); ?>" crossorigin>
    
    <script>if (!matchMedia('(prefers-reduced-motion: reduce)').matches) document.documentElement.classList.add('reveal-on');</script>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php echo $__env->yieldPushContent('head'); ?>

    <script type="application/ld+json"><?php echo \App\Support\Seo::json(\App\Support\Seo::organization()); ?></script>
    <?php echo $__env->yieldPushContent('schema'); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('google_analytics_id'))): ?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo e(setting('google_analytics_id')); ?>"></script>
        <script>window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', <?php echo \Illuminate\Support\Js::from(setting('google_analytics_id'))->toHtml() ?>);</script>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</head>
<body class="flex min-h-screen flex-col">
    <div id="page-progress" class="page-progress" aria-hidden="true"></div>
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] focus:rounded-lg focus:bg-white focus:px-4 focus:py-2 focus:shadow-lg">Skip to content</a>

    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main id="main" class="flex-1">
        <?php echo $__env->make('partials.flash', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php echo e($slot); ?>

    </main>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('partials.floating', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH D:\desktop projects\onthrill\resources\views/components/layout.blade.php ENDPATH**/ ?>