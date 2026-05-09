@props(['categoria'])

{{-- Fundo gradiente do gênero --}}
<div class="absolute inset-0 bg-gradient-to-br {{ $categoria['de'] }} {{ $categoria['para'] }}
            transition-transform duration-300 group-hover:scale-105"></div>

{{-- Ícone central --}}
<div class="absolute inset-0 flex items-center justify-center
            opacity-30 text-6xl pointer-events-none select-none">
    {{ $categoria['icone'] }}
</div>

{{-- Overlay escuro no rodapé --}}
<div class="absolute inset-x-0 bottom-0 h-20
            bg-gradient-to-t from-black/80 to-transparent"></div>

{{-- Nome do gênero --}}
<div class="absolute bottom-0 inset-x-0 p-4 flex items-center justify-between">
    <span class="text-white font-semibold text-sm leading-tight">
        {{ $categoria['label'] }}
    </span>
    <span class="text-zinc-300 text-xs">&#8594;</span>
</div>
