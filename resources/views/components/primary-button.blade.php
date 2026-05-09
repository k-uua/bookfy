@props([
    'type' => 'submit',
    'block' => false,
])

<button
    type="{{ $type }}"
    {{ $attributes->class([
        'bg-blue-600 hover:bg-blue-500 active:bg-blue-700',
        'text-white font-semibold text-sm',
        'px-6 py-2.5 rounded-lg',
        'shadow-lg shadow-blue-900/30',
        'transition-colors',
        'w-full' => $block,
    ]) }}
>
    {{ $slot }}
</button>
