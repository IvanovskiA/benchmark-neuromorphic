@props(['label', 'value', 'icon' => null])

<x-ui.card {{ $attributes->merge(['class' => 'hover:shadow-card-hover transition-shadow']) }}>
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="mt-2 text-3xl font-bold text-slate-900">{{ $value }}</p>
</x-ui.card>
