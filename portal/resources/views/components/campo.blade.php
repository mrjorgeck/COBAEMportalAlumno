@props([
    'for',
    'label',
    'required' => false,
    'help' => null,
    'error' => null,
    'name' => null,
])

@php
    $errorKey = $name ?? (str_starts_with($for, 'form_') ? 'form.'.substr($for, 5) : $for);
    $errorMessage = $error ?? $errors->first($errorKey);
@endphp

<div {{ $attributes->merge(['class' => 'space-y-1']) }}>
    <label for="{{ $for }}" class="block text-sm font-medium text-gray-800">
        {{ $label }} <x-obligatorio :required="$required" />
    </label>

    {{ $slot }}

    @if ($help)
        <p id="{{ $for }}_ayuda" class="text-xs text-gray-600">{{ $help }}</p>
    @endif

    @if ($errorMessage)
        <p id="{{ $for }}_error" class="text-sm text-red-700">{{ $errorMessage }}</p>
    @endif
</div>
