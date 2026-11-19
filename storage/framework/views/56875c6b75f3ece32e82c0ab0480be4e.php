<?php
    $user = auth()->user();
    // A customer account that opened the admin panel gets an explanation and a way out.
    $customerAtAdmin = $user && ! $user->isStaff() && request()->is('admin', 'admin/*');
?>

<?php if (isset($component)) { $__componentOriginal23a33f287873b564aaf305a1526eada4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal23a33f287873b564aaf305a1526eada4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layout','data' => ['title' => 'Not allowed','noindex' => true]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Not allowed','noindex' => true]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

    <div class="container-page section">
        <div class="mx-auto max-w-xl text-center">
            <p class="font-display text-7xl font-bold text-saffron-500">403</p>

            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($customerAtAdmin): ?>
                <h1 class="mt-4 text-3xl font-bold sm:text-4xl">This account cannot open the admin panel</h1>
                <p class="mt-4">
                    You are signed in as <strong class="text-ink"><?php echo e($user->email); ?></strong>, which is a customer account.
                    The admin panel is only for staff accounts.
                </p>
                <p class="mt-3 text-sm">
                    To manage the website, sign out and sign in with your admin account.
                    If this account should have admin access, the Super Admin can give it a role under Settings &gt; Users.
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <form action="<?php echo e(route('logout')); ?>" method="post">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="next" value="admin-login">
                        <button type="submit" class="btn btn-primary">Sign out and go to admin login</button>
                    </form>
                    <a href="<?php echo e(route('account.index')); ?>" class="btn btn-outline">My Account</a>
                </div>
            <?php else: ?>
                <h1 class="mt-4 text-3xl font-bold sm:text-4xl">You do not have permission to open this page</h1>
                <p class="mt-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user?->isStaff()): ?>
                        Your role does not include this part of the admin panel. Ask the Super Admin if you need it.
                    <?php else: ?>
                        This page is not available to your account.
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($user?->isStaff()): ?>
                        <a href="<?php echo e(url('/admin')); ?>" class="btn btn-primary">Back to the admin panel</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <a href="<?php echo e(route('home')); ?>" class="btn btn-outline">Go to Home</a>
                </div>
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
<?php /**PATH D:\desktop projects\onthrill\resources\views\errors/403.blade.php ENDPATH**/ ?>