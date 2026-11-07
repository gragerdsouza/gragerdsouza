@props(['name', 'label', 'type' => 'text'])

@php
    $classes = 'mt-1.5 block w-full rounded-md border bg-ink-900 px-3 py-2 text-sm text-white placeholder:text-ink-500 focus:outline-none focus:ring-2 focus:ring-accent-400 '
        .($errors->has($name) ? 'border-red-400' : 'border-white/10');
@endphp

<div>
    <label for="{{ $name }}" class="block text-sm font-medium text-ink-200">{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="5" {{ $attributes->merge(['class' => $classes]) }} @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>{{ old($name) }}</textarea>
    @else
        <input id="{{ $name }}" name="{{ $name }}" type="{{ $type }}" value="{{ old($name) }}" {{ $attributes->merge(['class' => $classes]) }} @error($name) aria-invalid="true" aria-describedby="{{ $name }}-error" @enderror>
    @endif
    @error($name)
        <p id="{{ $name }}-error" class="mt-1 text-sm text-red-400">{{ $message }}</p>
    @enderror
</div>
