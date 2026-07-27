<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['label' => '', 'name' => '', 'options' => [], 'selected' => null]));

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

foreach (array_filter((['label' => '', 'name' => '', 'options' => [], 'selected' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<div>
    <?php if($label): ?>
        <label for="<?php echo e($name); ?>" class="mb-1 block text-sm font-medium text-slate-700"><?php echo e($label); ?></label>
    <?php endif; ?>
    <select name="<?php echo e($name); ?>" id="<?php echo e($name); ?>" <?php echo e($attributes->merge(['class' => 'block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500'])); ?>>
        <option value="">-- Select --</option>
        <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($value); ?>" <?php if((string)$selected === (string)$value): echo 'selected'; endif; ?>><?php echo e($text); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
</div>
<?php /**PATH /var/www/html/resources/views/components/ui/select.blade.php ENDPATH**/ ?>