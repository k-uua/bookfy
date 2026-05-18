@props([
    'usuario',
    'size' => 'md',
])

@php
    $sizes = [
        'xs'  => ['box' => 'w-6 h-6',                     'text' => 'text-[10px]'],
        'sm'  => ['box' => 'w-7 h-7',                     'text' => 'text-[11px]'],
        'md'  => ['box' => 'w-8 h-8',                     'text' => 'text-xs'],
        'lg'  => ['box' => 'w-9 h-9',                     'text' => 'text-sm'],
        '2xl' => ['box' => 'w-20 h-20',                   'text' => 'text-3xl'],
        'xl'  => ['box' => 'w-28 h-28 sm:w-32 sm:h-32',  'text' => 'text-5xl sm:text-6xl'],
    ];
    $s = $sizes[$size] ?? $sizes['md'];
@endphp

<div {{ $attributes->class([$s['box'], 'rounded-full overflow-hidden shrink-0 flex items-center justify-center']) }}>
    @if (!empty($usuario->foto_perfil))
        <img src="{{ Storage::url($usuario->foto_perfil) }}"
             alt="Foto de {{ $usuario->nome }}"
             class="w-full h-full object-cover">
    @else
        <span class="font-bold text-white leading-none select-none {{ $s['text'] }}">
            {{ mb_strtoupper(mb_substr($usuario->nome ?? '?', 0, 1)) }}
        </span>
    @endif
</div>
