<aside id="sidebar" class="fixed inset-y-0 left-0 z-30 hidden bg-brand-900 text-white lg:block">
    <div class="flex h-16 items-center border-b border-brand-700 px-6">
        <div class="sidebar-brand whitespace-nowrap">
            <p class="text-xs uppercase tracking-widest text-blue-200">Neuromorphic</p>
            <p class="text-sm font-semibold">Benchmark</p>
        </div>
    </div>
    <nav class="space-y-1 p-4">
        <?php
            $links = [
                ['route' => 'benchmarks.index', 'label' => 'Dashboard'],
                ['route' => 'benchmarks.create', 'label' => 'New Benchmark'],
                ['route' => 'benchmarks.history', 'label' => 'History'],
                ['route' => 'benchmarks.charts', 'label' => 'Charts'],
                ['route' => 'methodology.index', 'label' => 'Methodology'],
            ];
        ?>
        <?php $__currentLoopData = $links; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $link): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route($link['route'], [], false)); ?>"
               class="block whitespace-nowrap rounded-lg px-4 py-2.5 text-sm font-medium transition <?php echo e(request()->routeIs($link['route']) ? 'bg-brand-700 text-white' : 'text-blue-100 hover:bg-brand-700/70'); ?>">
                <?php echo e($link['label']); ?>

            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>
</aside>
<?php /**PATH /var/www/html/resources/views/partials/sidebar.blade.php ENDPATH**/ ?>