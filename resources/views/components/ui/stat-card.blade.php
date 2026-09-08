@props(['label', 'value', 'icon' => null])

<x-ui.card {{ $attributes->merge(['class' => 'min-w-0 hover:shadow-card-hover transition-shadow']) }}>
    <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
    <p class="mt-2 text-xl font-bold leading-snug text-slate-900" style="overflow-wrap: anywhere;">{{ $value }}</p>
</x-ui.card>
