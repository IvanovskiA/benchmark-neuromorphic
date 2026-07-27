@props(['label' => '', 'name' => '', 'options' => [], 'selected' => null])

<div>
    @if($label)
        <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $label }}</label>
    @endif
    <select name="{{ $name }}" id="{{ $name }}" {{ $attributes->merge(['class' => 'block w-full rounded-lg border-slate-300 shadow-sm focus:border-brand-500 focus:ring-brand-500']) }}>
        <option value="">-- Select --</option>
        @foreach($options as $value => $text)
            <option value="{{ $value }}" @selected((string)$selected === (string)$value)>{{ $text }}</option>
        @endforeach
    </select>
</div>
