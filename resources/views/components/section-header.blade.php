@props([
    'titulo',
    'subtitulo'   => null,
    'comNav'      => false,
    'navTarget'   => null,   // ID do contêiner que rola
])

<div class="flex items-start justify-between mb-8 gap-4">
    <div>
        <h2 class="text-2xl font-bold text-white">{{ $titulo }}</h2>
        @if ($subtitulo)
            <p class="text-zinc-400 text-sm mt-1.5 max-w-md">{{ $subtitulo }}</p>
        @endif
    </div>

    @if ($comNav && $navTarget)
        <div class="flex gap-2 mt-1 shrink-0">
            <button type="button"
                    data-carousel-prev="{{ $navTarget }}"
                    class="w-8 h-8 rounded-full border border-zinc-700 text-zinc-400
                           hover:border-white hover:text-white flex items-center justify-center
                           text-sm transition-colors"
                    aria-label="Anterior">
                &#8592;
            </button>
            <button type="button"
                    data-carousel-next="{{ $navTarget }}"
                    class="w-8 h-8 rounded-full border border-zinc-700 text-zinc-400
                           hover:border-white hover:text-white flex items-center justify-center
                           text-sm transition-colors"
                    aria-label="Próximo">
                &#8594;
            </button>
        </div>
    @endif
</div>
