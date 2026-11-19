
<?php
    $b = $booking;
    $amount = $b->advance ?? $b->total;
    $india = $b->currency === 'INR';

    if ($india) {
        $qr = media_url(setting('upi_qr'));
        $ids = array_filter(['UPI ID' => trim((string) setting('upi_id')), 'UPI number' => trim((string) setting('upi_number'))]);
        $payee = trim((string) setting('upi_name')) ?: setting('site_name');
        $bank = trim((string) setting('bank_details_india'));
        // Opens the traveller's UPI app (phones only) with payee, amount and booking reference filled in.
        $upiLink = filled(setting('upi_id')) && $amount ? 'upi://pay?'.http_build_query([
            'pa' => trim((string) setting('upi_id')), 'pn' => $payee, 'am' => number_format((float) $amount, 2, '.', ''),
            'cu' => 'INR', 'tn' => 'Booking '.$b->reference,
        ], '', '&', PHP_QUERY_RFC3986) : null;
    } else {
        $qr = media_url(setting('nepal_qr'));
        $ids = array_filter(['eSewa ID' => trim((string) setting('esewa_id')), 'Khalti ID' => trim((string) setting('khalti_id'))]);
        $payee = setting('site_name');
        $bank = trim((string) setting('bank_details_nepal'));
        $upiLink = null;
    }
    $hasDetails = $qr || $ids || $bank !== '';
?>

<div class="card border-saffron-300 bg-saffron-50 p-6">
    <h2 class="text-xl font-bold"><?php echo e(wording('pay_title')); ?></h2>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($amount): ?>
        <p class="mt-1 text-sm"><?php echo e(wording_html('pay_text', ['amount' => $b->money($amount), 'reference' => $b->reference])); ?></p>
    <?php else: ?>
        <p class="mt-1 text-sm"><?php echo e(wording_html('pay_text_request', ['reference' => $b->reference])); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($qr || $ids): ?>
        <div class="<?php echo \Illuminate\Support\Arr::toCssClasses(['mt-5 grid items-start gap-6', 'sm:grid-cols-[auto_1fr]' => $qr]); ?>">
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($qr): ?>
                <figure class="mx-auto w-52 rounded-lg border border-navy-200 bg-white p-3 text-center sm:mx-0">
                    <img src="<?php echo e($qr); ?>" alt="<?php echo e($india ? 'UPI' : 'eSewa / Khalti'); ?> QR code" width="184" height="184" class="mx-auto size-46 object-contain">
                    <figcaption class="mt-2 text-xs text-ink/60"><?php echo e($india ? wording('pay_scan_upi') : wording('pay_scan_nepal')); ?></figcaption>
                </figure>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            <div>
                <dl class="divide-y divide-navy-200 rounded-lg border border-navy-200 bg-white text-sm">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $ids; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $label => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                        <div class="flex items-center justify-between gap-3 px-4 py-3" x-data="{ copied: false }">
                            <div class="min-w-0">
                                <dt class="text-navy-500"><?php echo e($label); ?></dt>
                                <dd class="font-semibold break-all text-ink"><?php echo e($value); ?></dd>
                            </div>
                            <button type="button" class="btn btn-outline btn-sm shrink-0"
                                    @click="navigator.clipboard?.writeText(<?php echo \Illuminate\Support\Js::from($value)->toHtml() ?>).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
                                    x-text="copied ? <?php echo \Illuminate\Support\Js::from(wording('pay_copied'))->toHtml() ?> : <?php echo \Illuminate\Support\Js::from(wording('pay_copy'))->toHtml() ?>"><?php echo e(wording('pay_copy')); ?></button>
                        </div>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    <div class="px-4 py-3">
                        <dt class="text-navy-500"><?php echo e(wording('pay_name')); ?></dt>
                        <dd class="font-semibold text-ink"><?php echo e($payee); ?></dd>
                    </div>
                </dl>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($upiLink): ?>
                    
                    <a href="<?php echo e($upiLink); ?>" class="btn btn-primary mt-4 w-full sm:hidden"><?php echo e(wording('pay_upi_button', ['amount' => $b->money($amount)])); ?></a>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($bank !== ''): ?>
        <h3 class="mt-5 text-sm font-semibold text-ink"><?php echo e(wording('pay_bank')); ?></h3>
        <p class="mt-1 text-sm whitespace-pre-line"><?php echo e($bank); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! $hasDetails): ?>
        <p class="mt-4 text-sm text-ink"><?php echo e(wording('pay_none')); ?></p>
    <?php elseif(filled(setting('payment_note'))): ?>
        <p class="mt-5 text-sm whitespace-pre-line text-ink"><?php echo e(setting('payment_note')); ?></p>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('whatsapp') ?: setting('phone'))): ?>
        <a href="<?php echo e(whatsapp_url("Namaste, I have paid the advance for booking {$b->reference}. Sharing the payment screenshot.")); ?>" target="_blank" rel="noopener" class="btn btn-whatsapp mt-5"><?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
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
<?php endif; ?> <?php echo e(wording('pay_screenshot')); ?></a>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>
<?php /**PATH D:\desktop projects\onthrill\resources\views/partials/payment-box.blade.php ENDPATH**/ ?>