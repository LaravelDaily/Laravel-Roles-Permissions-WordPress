@props(['label', 'name', 'value' => null, 'checked' => false, 'required' => false])

<label for="{{ $name }}" {{ $attributes->merge(['class' => 'ml-2 block text-sm text-gray-700 dark:text-gray-300']) }}>
    @if($required)
        <input type="hidden" name="{{ $name }}" value="0">
    @endif
    <input type="checkbox" id="{{ $name }}" name="{{ $name }}" value="{{ $value }}" {{ $attributes }}
        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" {{ $checked ? 'checked' : '' }}>
    {{ $label }}
</label>

@error($name)
    <span class="text-red-500">{{ $message }}</span>
@enderror