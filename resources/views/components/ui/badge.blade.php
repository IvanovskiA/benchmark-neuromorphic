@props(['status' => 'pending'])

@php
    $classes = match($status) {
        'completed' => 'bg-emerald-100 text-emerald-800',
        'running' => 'bg-amber-100 text-amber-800',
        'failed' => 'bg-red-100 text-red-800',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {$classes}"]) }}>
    {{ ucfirst($status) }}
</span>
