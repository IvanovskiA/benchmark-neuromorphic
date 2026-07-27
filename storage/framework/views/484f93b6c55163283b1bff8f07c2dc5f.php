<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['runs']));

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

foreach (array_filter((['runs']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-200 text-sm">
        <thead class="bg-slate-100">
            <tr>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">ID</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">Dataset</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">Architecture</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">Status</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">F1</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">Latency</th>
                <th class="px-4 py-3 text-left font-semibold text-slate-600">Date</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 bg-white">
            <?php $__currentLoopData = $runs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $run): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="hover:bg-slate-50 <?php echo e($run->architecture->is_neuromorphic ? 'border-l-4 border-violet-500' : ''); ?>">
                    <td class="px-4 py-3 font-mono text-xs text-slate-500"><?php echo e(\Illuminate\Support\Str::limit($run->id, 8)); ?></td>
                    <td class="px-4 py-3"><?php echo e($run->dataset->name); ?></td>
                    <td class="px-4 py-3"><?php echo e($run->architecture->name); ?></td>
                    <td class="px-4 py-3"><?php if (isset($component)) { $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.ui.badge','data' => ['status' => $run->status]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('ui.badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($run->status)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $attributes = $__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__attributesOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4)): ?>
<?php $component = $__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4; ?>
<?php unset($__componentOriginalab7baa01105b3dfe1e0cf1dfc58879b4); ?>
<?php endif; ?></td>
                    <td class="px-4 py-3 font-mono"><?php echo e($run->metric?->f1_score !== null ? \App\Support\MetricsFormat::f1($run->metric->f1_score) : '—'); ?></td>
                    <td class="px-4 py-3 font-mono"><?php echo e($run->metric?->latency_ms !== null ? \App\Support\MetricsFormat::latency($run->metric->latency_ms) : '—'); ?></td>
                    <td class="px-4 py-3"><?php echo e($run->created_at->format('Y-m-d H:i')); ?></td>
                    <td class="px-4 py-3 text-right">
                        <a href="<?php echo e(route('benchmarks.show', $run, false)); ?>" class="text-brand-600 hover:text-brand-700">Details</a>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<?php if(method_exists($runs, 'links')): ?>
    <div class="mt-4"><?php echo e($runs->links()); ?></div>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/components/benchmark/runs-table.blade.php ENDPATH**/ ?>