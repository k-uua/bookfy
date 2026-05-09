@props([
    'nota'  => 0,         // valor numérico (0-5, aceita decimal — 4.5)
    'total' => 5,
    'size'  => 'sm',      // sm | md | lg
])

@php
    $tamanhos = [
        'sm' => 'w-3.5 h-3.5',
        'md' => 'w-4 h-4',
        'lg' => 'w-5 h-5',
    ];
    $classeTamanho = $tamanhos[$size] ?? $tamanhos['sm'];
    $percentual    = max(0, min(100, ((float) $nota / $total) * 100));

    $svgPath = 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z';
@endphp

<div {{ $attributes->class('relative inline-flex items-center gap-0.5') }}>

    {{-- Camada de fundo: estrelas vazias --}}
    @for ($i = 1; $i <= $total; $i++)
        <svg class="{{ $classeTamanho }} text-zinc-700" fill="currentColor" viewBox="0 0 20 20">
            <path d="{{ $svgPath }}"/>
        </svg>
    @endfor

    {{-- Camada de cima: estrelas preenchidas, recortadas pelo percentual --}}
    <div class="absolute inset-0 flex items-center gap-0.5 overflow-hidden pointer-events-none"
         style="clip-path: inset(0 {{ 100 - $percentual }}% 0 0);">
        @for ($i = 1; $i <= $total; $i++)
            <svg class="{{ $classeTamanho }} text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="{{ $svgPath }}"/>
            </svg>
        @endfor
    </div>

</div>
