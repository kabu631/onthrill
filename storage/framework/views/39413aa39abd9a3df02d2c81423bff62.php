<?php
    $b = $booking;
    $inAccount ??= false;
    $rows = array_filter([
        wording('bookingpage_row_tour') => $b->package ? '<a href="'.e($b->package->url()).'" class="font-medium text-saffron-700 underline underline-offset-2">'.e($b->package_name).'</a>' : e($b->package_name),
        wording('bookingpage_row_date') => e($b->travel_date?->format('l, d F Y')),
        wording('bookingpage_row_travellers') => e($b->adults.' '.\Illuminate\Support\Str::plural('adult', $b->adults).($b->children ? ', '.$b->children.' '.\Illuminate\Support\Str::plural('child', $b->children) : '')),
        wording('bookingpage_row_nationality') => e(\App\Models\Booking::NATIONALITIES[$b->nationality] ?? $b->nationality),
        wording('bookingpage_row_from') => $b->travelling_from ? e($b->travelling_from) : null,
        wording('bookingpage_row_total') => $b->total !== null ? e($b->money($b->total)).' <span class="text-ink/55">('.e($b->money($b->price_per_person)).' '.e(wording('price_per_person')).')</span>' : e(wording('price_on_request')),
        wording('bookingpage_row_status') => e($b->statusLabel()).' · '.e(\App\Models\Booking::PAYMENT_STATUSES[$b->payment_status] ?? $b->payment_status),
    ]);
?>

<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['title' => wording('bookingpage_title', ['reference' => $b->reference]),'noindex' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('bookingpage_title', ['reference' => $b->reference])),'noindex' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($inAccount): ?>
        <?php if (isset($component)) { $__componentOriginalb00c7f6baf7ea834a0251f4153d10b8b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalb00c7f6baf7ea834a0251f4153d10b8b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-banner','data' => ['title' => wording('bookingpage_title', ['reference' => $b->reference]),'crumbs' => [wording('account_title') => route('account.index'), $b->reference => null]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-banner'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('bookingpage_title', ['reference' => $b->reference])),'crumbs' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute([wording('account_title') => route('account.index'), $b->reference => null])]); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['container-page section', 'pt-8 sm:pt-10' => $inAccount]); ?>">
        <div data-reveal class="mx-auto max-w-3xl">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($inAccount)): ?>
                <div class="text-center">
                    <h1 class="text-3xl font-bold sm:text-4xl"><?php echo e(wording('bookingpage_thanks', ['name' => \Illuminate\Support\Str::of($b->name)->before(' ')])); ?></h1>
                    <p class="mt-3"><?php echo e(wording_html('bookingpage_received', ['reference' => $b->reference, 'email' => $b->email, 'phone' => $b->phone])); ?></p>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="card mt-8 p-6">
                <h2 class="text-xl font-bold"><?php echo e(wording('bookingpage_details')); ?></h2>
                <dl class="mt-4 divide-y divide-navy-100 text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $rows; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="grid gap-1 py-3 sm:grid-cols-[10rem_1fr]">
                            <dt class="text-navy-500"><?php echo e($label); ?></dt>
                            <dd class="text-ink"><?php echo $value; ?></dd>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                </dl>
            </div>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $b->isPaid() && $b->status !== 'cancelled'): ?>
                <div class="mt-6">
                    <?php echo $__env->make('partials.payment-box', ['booking' => $b], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="<?php echo e(route('home')); ?>" class="btn btn-outline"><?php echo e(wording('bookingpage_home')); ?></a>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('account.index')); ?>" class="btn btn-dark"><?php echo e(wording('bookingpage_account')); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
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
<?php /**PATH D:\desktop projects\onthrill\resources\views/bookings/show.blade.php ENDPATH**/ ?>