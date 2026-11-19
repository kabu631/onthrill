
<div x-data="backToTop" class="fixed right-4 z-30 flex flex-col items-center gap-3 sm:right-6" style="bottom: calc(1.25rem + var(--bottom-bar, 0px))">
    <button type="button" x-cloak x-show="visible" @click="toTop"
            x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-y-3 opacity-0" x-transition:enter-end="translate-y-0 opacity-100"
            x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="grid size-11 place-items-center rounded-full bg-navy-800 text-white shadow-lift transition-colors hover:bg-navy-900"
            aria-label="Back to top" title="Back to top">
        <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'arrow-up','class' => 'size-5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'arrow-up','class' => 'size-5']); ?>
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
    </button>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(filled(setting('whatsapp') ?: setting('phone'))): ?>
        <a href="<?php echo e(whatsapp_url(wording('whatsapp_greeting'))); ?>" target="_blank" rel="noopener"
           class="grid size-14 place-items-center rounded-full bg-whatsapp text-white shadow-lift transition-colors hover:bg-[#1ebe5b]"
           aria-label="Chat with us on WhatsApp" title="Chat on WhatsApp">
            <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'whatsapp','class' => 'size-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'whatsapp','class' => 'size-8']); ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
</div>


<div x-data class="pointer-events-none fixed inset-x-0 top-24 z-[60] flex flex-col items-center gap-2 px-4" aria-live="polite">
    <template x-for="toast in $store.toast.items" :key="toast.id">
        <div x-transition.opacity
             class="pointer-events-auto flex max-w-md items-center gap-3 rounded-md px-5 py-3 text-sm font-medium text-white shadow-lift"
             :class="toast.type === 'error' ? 'bg-red-600' : 'bg-navy-900'">
            <span x-text="toast.message"></span>
        </div>
    </template>
</div>
<?php /**PATH D:\desktop projects\onthrill\resources\views/partials/floating.blade.php ENDPATH**/ ?>