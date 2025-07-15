@props([
    'label',
    'name',
    'placeholder' => '',
    'error' => false,
    'class' => '',
    'labelClass' => '',
    'rows' => 3,
    'value' => '',
])

@if ($label)
    <label for="{{ $name }}"
        {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700 mb-1 ' . $labelClass]) }}>
        {{ $label }}
    </label>
@endif

<textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}"
    {{ $attributes->merge(['class' => 'w-full px-4 py-2 rounded-lg text-gray-700 bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-vertical']) }}>{{ $value ?? $slot }}</textarea>

@error($name)
    <span class="text-red-500">{{ $message }}</span>
@enderror