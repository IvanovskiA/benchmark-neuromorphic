@props(['variant' => 'primary', 'type' => 'button'])

@php
    $classes = match($variant) {
        'danger' => 'bg-red-600 hover:bg-red-700 text-white',
        'secondary' => 'bg-white border border-slate-300 text-slate-700 hover:bg-slate-50',
        default => 'bg-brand-600 hover:bg-brand-700 text-white',
    };
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-lg px-5 py-2.5 text-sm font-medium shadow-sm transition {$classes}"]) }}>
    {{ $slot }}
</button>
