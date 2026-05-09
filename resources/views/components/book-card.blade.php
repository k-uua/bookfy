@props([
    'livro',
    'showRating' => false,
    'showDate'   => false,
    'href'       => null,   // se nulo, usa rota livros.show com o id do livro
])

@php
    $url = $href ?? ($livro['id'] ? route('livros.show', $livro['id']) : '#');
@endphp

<a href="{{ $url }}" class="shrink-0 w-40 sm:w-44 group block">

    <div class="aspect-[2/3] rounded-2xl overflow-hidden bg-zinc-900 relative
                ring-1 ring-zinc-800/60">
        @if (!empty($livro['capa']))
            <img src="{{ $livro['capa'] }}"
                 alt="{{ $livro['titulo'] }}"
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                 loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-zinc-700 text-4xl">
                📖
            </div>
        @endif
    </div>

    @if ($showRating)
        <div class="flex items-center gap-1 mt-2.5 px-1">
            @for ($i = 1; $i <= 5; $i++)
                <svg class="w-3.5 h-3.5 {{ $i <= floor($livro['rating']) ? 'text-amber-400' : 'text-zinc-700' }}"
                     fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
            @endfor
            @if (!empty($livro['rating']))
                <span class="text-zinc-300 text-xs ml-1 font-medium">{{ number_format($livro['rating'], 1) }}</span>
            @endif
        </div>
    @endif

    @if ($showDate && !empty($livro['lancamento']))
        <p class="text-zinc-400 text-xs mt-2.5 px-1">
            Lançado em {{ $livro['lancamento'] }}
        </p>
    @endif
</a>
