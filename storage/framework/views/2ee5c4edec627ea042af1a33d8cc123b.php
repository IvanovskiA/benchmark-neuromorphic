<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'primary', 'type' => 'button']));

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

foreach (array_filter((['variant' => 'primary', 'type' => 'button']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars); ?>

<?php
    $classes = match($variant) {
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        'secondary' => 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50',
        default => 'bg-brand-600 hover:bg-brand-700 text-white',
    };
?>

<button type="<?php echo e($type); ?>" <?php echo e($attributes->merge(['class' => "inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium shadow-sm transition {$classes}"])); ?>>
    <?php echo e($slot); ?>

</button>
<?php /**PATH /var/www/html/resources/views/components/ui/button.blade.php ENDPATH**/ ?>