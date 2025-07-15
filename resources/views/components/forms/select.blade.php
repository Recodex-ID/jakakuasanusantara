@props([
    'label',
    'name',
    'placeholder' => '',
    'error' => false,
    'class' => '',
    'labelClass' => '',
    'options' => [],
    'selected' => null,
])

@if ($label)
    <label for="{{ $name }}"
        {{ $attributes->merge(['class' => 'block text-sm font-medium text-gray-700 mb-1 ' . $labelClass]) }}>
        {{ $label }}
    </label>
@endif

<div class="relative">
    <select id="{{ $name }}" name="{{ $name }}"
        {{ $attributes->merge(['class' => 'w-full px-4 py-2 pr-10 rounded-lg text-gray-700 bg-gray-50 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent appearance-none']) }}>
        @if ($placeholder)
            <option value="">{{ $placeholder }}</option>
        @endif
        
        @if (!empty($options))
            @foreach ($options as $value => $text)
                <option value="{{ $value }}" {{ $selected == $value ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @endforeach
        @else
            {{ $slot }}
        @endif
    </select>
    
    <!-- Custom dropdown arrow -->
    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
        <x-fas-chevron-down class="w-4 h-4 text-gray-400" />
    </div>
</div>

@error($name)
    <span class="text-red-500">{{ $message }}</span>
@enderror