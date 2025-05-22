@props(['type' => 'submit', 'class' => '', 'tag' => 'button'])
<{{ $tag }} type="{{ $type }}"
    {{ $attributes->merge(['class' => 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors ' . $class]) }}>
    {{ $slot }}
    </{{ $tag }}>
