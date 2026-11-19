
<?php
    $compact = $compact ?? false;
    $grid = $compact ? '' : 'sm:grid-cols-2';
    $full = $compact ? '' : 'sm:col-span-2';
    $about = isset($package) ? ['label' => wording('enquiry_tour_label'), 'name' => $package->name, 'icon' => 'map']
        : (isset($destination) ? ['label' => wording('enquiry_place_label'), 'name' => $destination->name, 'icon' => 'map-pin'] : null);
?>

<form id="enquiry-form" action="<?php echo e(route('inquiry.store')); ?>" method="post" class="mt-5 grid scroll-mt-44 gap-4 <?php echo e($grid); ?>"
      x-data="{ sending: false }" @submit="sending = true">
    <?php echo csrf_field(); ?>

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('inquiry_sent')): ?>
        <div role="status" class="flex items-start gap-3 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 <?php echo e($full); ?>">
            <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => 'check-circle','class' => 'mt-0.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'check-circle','class' => 'mt-0.5']); ?>
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
            <p><?php echo e(session('inquiry_sent')); ?></p>
        </div>
    <?php elseif($errors->hasAny(['name', 'phone', 'email', 'message', 'package_id', 'destination_id'])): ?>
        <div role="alert" class="rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm font-medium text-red-800 <?php echo e($full); ?>">
            <?php echo e(wording('form_error')); ?>

        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    
    <div class="hidden" aria-hidden="true">
        <label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($about): ?>
        <div class="<?php echo e($full); ?>">
            <p class="label"><?php echo e($about['label']); ?></p>
            <p class="flex items-center gap-2.5 rounded-md border border-navy-200 bg-mist px-3.5 py-3 text-sm font-semibold text-navy-900">
                <?php if (isset($component)) { $__componentOriginal1effc256f38910ae5c52d1af950df88b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1effc256f38910ae5c52d1af950df88b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.op-icon','data' => ['name' => $about['icon'],'class' => 'size-4 text-saffron-600']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('op-icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($about['icon']),'class' => 'size-4 text-saffron-600']); ?>
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
<?php endif; ?> <?php echo e($about['name']); ?>

            </p>
        </div>
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(isset($package)): ?>
            <input type="hidden" name="package_id" value="<?php echo e($package->id); ?>">
        <?php else: ?>
            <input type="hidden" name="destination_id" value="<?php echo e($destination->id); ?>">
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'name','label' => wording('enquiry_name'),'required' => true,'autocomplete' => 'name','maxlength' => '120']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'name','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('enquiry_name')),'required' => true,'autocomplete' => 'name','maxlength' => '120']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'phone','label' => wording('enquiry_phone'),'type' => 'tel','required' => true,'autocomplete' => 'tel','inputmode' => 'tel','maxlength' => '20']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'phone','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('enquiry_phone')),'type' => 'tel','required' => true,'autocomplete' => 'tel','inputmode' => 'tel','maxlength' => '20']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'email','label' => wording('enquiry_email'),'type' => 'email','autocomplete' => 'email','maxlength' => '160']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'email','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('enquiry_email')),'type' => 'email','autocomplete' => 'email','maxlength' => '160']); ?>
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

    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if (! ($about)): ?>
        <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'package_id','label' => wording('enquiry_topic')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'package_id','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('enquiry_topic'))]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

            <select id="package_id" name="package_id" class="input">
                <option value=""><?php echo e(wording('enquiry_general')); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $packages ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                    <option value="<?php echo e($option->id); ?>" <?php if((int) old('package_id') === $option->id): echo 'selected'; endif; ?>><?php echo e($option->name); ?></option>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
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
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalae4c123bc9806121d87d234de2f27a3b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalae4c123bc9806121d87d234de2f27a3b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.field','data' => ['name' => 'message','label' => wording('enquiry_message'),'required' => true,'class' => ''.e($full).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('field'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'message','label' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(wording('enquiry_message')),'required' => true,'class' => ''.e($full).'']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

        <textarea id="message" name="message" rows="<?php echo e($compact ? 4 : 5); ?>" required maxlength="3000" class="input" placeholder="<?php echo e(wording('enquiry_placeholder')); ?>"><?php echo e(old('message')); ?></textarea>
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

    <div class="<?php echo e($full); ?>">
        <button type="submit" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['btn btn-primary', 'w-full' => $compact, 'w-full sm:w-auto' => ! $compact]); ?>" :disabled="sending">
            <span x-show="!sending"><?php echo e(wording('enquiry_button')); ?></span>
            <span x-cloak x-show="sending"><?php echo e(wording('form_sending')); ?></span>
        </button>
    </div>
</form>
<?php /**PATH D:\desktop projects\onthrill\resources\views/partials/inquiry-form.blade.php ENDPATH**/ ?>