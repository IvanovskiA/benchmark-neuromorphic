<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['type' => 'success', 'message' => '']));

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

foreach (array_filter((['type' => 'success', 'message' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $classes = match($type) {
        'error' => 'border-red-200 bg-red-50 text-red-800',
        default => 'border-emerald-200 bg-emerald-50 text-emerald-800',
    };
?>

<div <?php echo e($attributes->merge(['class' => "mb-6 rounded-lg border px-4 py-3 text-sm {$classes}"])); ?>>
    <?php echo e($message); ?>

</div>
<?php /**PATH /var/www/html/resources/views/components/ui/alert.blade.php ENDPATH**/ ?>