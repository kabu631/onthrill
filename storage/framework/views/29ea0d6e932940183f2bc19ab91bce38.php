
<div class="card border-navy-300 bg-mist p-5">
    <p class="font-semibold text-ink">Booking is turned off for staff accounts</p>
    <p class="mt-1 text-sm">You are signed in as <?php echo e(auth()->user()->name); ?>, a staff account. To try a booking, sign out or use a customer account.</p>
    <div class="mt-4 flex flex-wrap gap-3">
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(! empty($editUrl)): ?>
            <a href="<?php echo e($editUrl); ?>" class="btn btn-dark btn-sm">Edit in admin</a>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        <form action="<?php echo e(route('logout')); ?>" method="post">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-outline btn-sm">Sign out</button>
        </form>
    </div>
</div>
<?php /**PATH D:\desktop projects\onthrill\resources\views/partials/staff-notice.blade.php ENDPATH**/ ?>