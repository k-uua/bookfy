@extends('layouts.main')
@section('titulo', 'Perfil — ' . $usuario->nome)
@section('conteudo')

{{-- ══════════════════════════════════════════════════════════
     HERO DO PERFIL
══════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-12">
    <div class="flex flex-col lg:flex-row items-start gap-8 lg:gap-12">

        {{-- Avatar + Nível + Botão --}}
        <div class="flex flex-col items-center gap-2 shrink-0 lg:w-36">

            {{-- Avatar com overlay de upload --}}
            <form action="{{ route('usuario.foto') }}" method="post"
                  enctype="multipart/form-data" id="form-foto">
                @csrf
                <input type="file" name="foto" id="foto-input"
                       accept="image/jpeg,image/png,image/webp"
                       class="sr-only"
                       onchange="document.getElementById('form-foto').submit()">

                <label for="foto-input"
                       class="relative group/avatar cursor-pointer block"
                       aria-label="Alterar foto de perfil">
                    <x-avatar :usuario="$usuario" size="xl"
                              class="bg-zinc-700 ring-4 ring-zinc-800" />

                    {{-- Overlay câmera --}}
                    <div class="absolute inset-0 rounded-full flex items-center justify-center
                                bg-black/50 opacity-0 group-hover/avatar:opacity-100 transition-opacity">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </label>
            </form>

            @error('foto')
                <p class="text-red-400 text-xs text-center mt-1">{{ $message }}</p>
            @enderror

            <span class="text-zinc-400 text-sm font-medium">Nivel: {{ $nivel }}</span>

            <a href="{{ route('usuario.editar') }}"
               class="mt-1 px-4 py-1.5 border border-zinc-600 rounded-full text-xs text-zinc-300
                      hover:border-zinc-400 hover:text-white transition-colors tracking-wide">
                Gerenciar conta
            </a>
        </div>

        {{-- Nome + XP + Conquistas --}}
        <div class="flex-1 min-w-0">
            <h1 class="text-white text-3xl sm:text-4xl font-bold tracking-tight leading-tight">
                {{ $usuario->nome }}
            </h1>

            {{-- Barra de XP --}}
            <div class="flex items-center gap-3 mt-3 max-w-sm">
                <div class="flex-1 bg-zinc-800 rounded-full h-1.5 overflow-hidden"
                     role="progressbar"
                     aria-valuenow="{{ $progresso }}"
                     aria-valuemin="0"
                     aria-valuemax="100"
                     aria-label="Progresso de experiência: {{ $xpNoNivel }} / {{ $xpParaSubir }} XP">
                    <div class="bg-blue-500 h-full rounded-full transition-all duration-500"
                         style="width: {{ $progresso }}%"></div>
                </div>
                <span class="text-zinc-400 text-xs shrink-0">experiencia</span>
            </div>

            {{-- Conquistas --}}
            <div class="mt-8">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-white text-lg font-semibold">Conquistas</h2>
                    @if ($conquistas->isNotEmpty())
                        <a href="{{ route('conquistas.index') }}"
                           class="text-xs text-zinc-400 hover:text-blue-400 transition-colors flex items-center gap-1">
                            Ver todas
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    @endif
                </div>

                @if ($conquistas->isEmpty())
                    <div class="flex flex-col items-start gap-3">
                        <p class="text-zinc-500 text-sm">Nenhuma conquista desbloqueada ainda. Continue lendo!</p>
                        <a href="{{ route('conquistas.index') }}"
                           class="text-xs text-blue-400 hover:text-blue-300 transition-colors flex items-center gap-1">
                            Ver conquistas disponíveis
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                @else
                    {{-- Ícones das últimas 6 conquistas --}}
                    <div class="flex flex-wrap items-end gap-3">
                        @foreach ($conquistas->take(6) as $conquista)
                            @php
                                $iconePath = $conquista->icone;
                                $nivelCor  = match($conquista->nivel_conquista) {
                                    'ouro'   => 'text-amber-400',
                                    'prata'  => 'text-zinc-300',
                                    default  => 'text-amber-600',
                                };
                            @endphp
                            <div class="relative group/badge">
                                {{-- Ícone sem fundo --}}
                                <div class="w-12 h-12 transition-transform duration-200
                                            group-hover/badge:scale-110 cursor-default">
                                    @if ($iconePath)
                                        <img src="{{ asset($iconePath) }}"
                                             alt="{{ $conquista->titulo }}"
                                             class="w-full h-full object-contain">
                                    @else
                                        {{-- fallback: medalha --}}
                                        <span class="text-4xl leading-none {{ $nivelCor }}">
                                            {{ $conquista->medalha() }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Tooltip --}}
                                <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-3 z-50 w-52
                                            opacity-0 group-hover/badge:opacity-100 pointer-events-none
                                            transition-opacity duration-150
                                            bg-zinc-900 border border-zinc-700/70 rounded-xl
                                            shadow-xl shadow-black/60 px-3.5 py-3">
                                    {{-- Seta --}}
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 w-0 h-0
                                                border-l-[6px] border-l-transparent
                                                border-r-[6px] border-r-transparent
                                                border-t-[6px] border-t-zinc-700/70"></div>

                                    {{-- Cabeçalho --}}
                                    <div class="flex items-center gap-1.5 mb-1.5">
                                        <span class="text-sm leading-none">{{ $conquista->medalha() }}</span>
                                        <span class="text-white text-xs font-semibold leading-tight">
                                            {{ $conquista->titulo }}
                                        </span>
                                    </div>

                                    {{-- Badge de nível --}}
                                    <span class="inline-block text-[10px] font-semibold rounded-full px-2 py-0.5 mb-2
                                                 {{ $nivelCor }}
                                                 bg-zinc-800 border border-zinc-700/60">
                                        {{ ucfirst($conquista->nivel_conquista) }}
                                    </span>

                                    {{-- Descrição --}}
                                    @if ($conquista->descricao)
                                        <p class="text-zinc-400 text-[11px] leading-snug">
                                            {{ $conquista->descricao }}
                                        </p>
                                    @endif

                                    {{-- XP --}}
                                    @if ($conquista->xp)
                                        <p class="text-blue-400 text-[10px] font-semibold mt-2">
                                            ⚡ +{{ $conquista->xp }} XP
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach

                        {{-- Link "ver mais" se houver mais de 6 --}}
                        @if ($conquistas->count() > 6)
                            <a href="{{ route('conquistas.index') }}"
                               class="text-zinc-600 hover:text-zinc-300 text-xs font-medium transition-colors
                                      leading-tight self-center">
                                +{{ $conquistas->count() - 6 }}
                            </a>
                        @endif
                    </div>

                    {{-- Estatísticas rápidas --}}
                    <div class="flex items-center gap-4 mt-3 text-xs text-zinc-600">
                        @php
                            $cBronze = $conquistas->filter(fn($c) => $c->nivel_conquista === 'bronze')->count();
                            $cPrata  = $conquistas->filter(fn($c) => $c->nivel_conquista === 'prata')->count();
                            $cOuro   = $conquistas->filter(fn($c) => $c->nivel_conquista === 'ouro')->count();
                        @endphp
                        @if ($cBronze) <span>🥉 {{ $cBronze }}</span> @endif
                        @if ($cPrata)  <span>🥈 {{ $cPrata }}</span>  @endif
                        @if ($cOuro)   <span>🥇 {{ $cOuro }}</span>   @endif
                        <span class="text-blue-500/70">⚡ {{ number_format($conquistas->sum('xp')) }} XP</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Estatísticas --}}
        <div class="flex gap-10 sm:gap-16 shrink-0 lg:flex-col lg:gap-8 lg:text-right">
            <div>
                <div class="text-white text-3xl sm:text-4xl font-bold tabular-nums">
                    {{ $usuario->notas->count() }}
                </div>
                <div class="text-zinc-400 text-sm mt-1">Avaliações</div>
            </div>
            <div>
                <div class="text-white text-3xl sm:text-4xl font-bold tabular-nums">
                    {{ $usuario->estantes->count() }}
                </div>
                <div class="text-zinc-400 text-sm mt-1">Bibliotecas</div>
            </div>
        </div>

    </div>
</section>

{{-- ══════════════════════════════════════════════════════════
     LIVROS FAVORITOS
══════════════════════════════════════════════════════════ --}}
@php $favs = $livrosFavoritos ?? collect(); @endphp

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14">

    <div class="flex items-center justify-between mb-5 gap-4">
        <h2 class="text-zinc-400 text-sm font-medium uppercase tracking-widest">
            Livros favoritos
        </h2>

        @if (! $favs->isEmpty())
            <div class="flex gap-2 shrink-0">
                <button type="button"
                        data-carousel-prev="carousel-favoritos"
                        class="w-8 h-8 rounded-full border border-zinc-700 text-zinc-400
                               hover:border-white hover:text-white flex items-center justify-center
                               text-sm transition-colors"
                        aria-label="Anterior">
                    &#8592;
                </button>
                <button type="button"
                        data-carousel-next="carousel-favoritos"
                        class="w-8 h-8 rounded-full border border-zinc-700 text-zinc-400
                               hover:border-white hover:text-white flex items-center justify-center
                               text-sm transition-colors"
                        aria-label="Próximo">
                    &#8594;
                </button>
            </div>
        @endif
    </div>

    @if ($favs->isEmpty())
        <p class="text-zinc-600 text-sm">Nenhum livro favorito adicionado ainda.</p>
    @else
        <div class="relative">

            <div id="carousel-favoritos"
                 class="flex gap-4 sm:gap-5 overflow-x-auto pb-3 scroll-smooth scrollbar-hide">

                @foreach ($favs as $livro)
                    @php
                        $notaFav = $usuario->notas->where('id_livro', $livro->id)->first();
                    @endphp
                    <a href="{{ route('livros.show', $livro->google_books_id) }}"
                       class="shrink-0 w-36 sm:w-40 group block focus:outline-none
                              focus-visible:ring-2 focus-visible:ring-red-500 rounded-2xl"
                       title="{{ $livro->titulo }}">

                        <div class="relative aspect-[2/3] rounded-2xl overflow-hidden bg-zinc-900
                                    ring-1 ring-zinc-800/60 transition-shadow group-hover:ring-zinc-700">
                            @if ($livro->capa_livro_url)
                                <img src="{{ $livro->capa_livro_url }}"
                                     alt="{{ $livro->titulo }}"
                                     class="w-full h-full object-cover transition-transform duration-300
                                            group-hover:scale-105"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center
                                            text-zinc-600 text-4xl">📖</div>
                            @endif

                            {{-- Ícone de coração no canto --}}
                            <div class="absolute top-2 right-2 w-6 h-6 rounded-full bg-black/60
                                        flex items-center justify-center">
                                <svg class="w-3.5 h-3.5 text-red-400" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                                </svg>
                            </div>
                        </div>

                        @if ($notaFav)
                            <div class="flex items-center gap-1 mt-2.5 px-0.5">
                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0"
                                     fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                <span class="text-zinc-300 text-xs font-medium">
                                    {{ number_format($notaFav->nota, 1) }}
                                </span>
                            </div>
                        @endif
                    </a>
                @endforeach

            </div>

            {{-- Gradiente direita --}}
            <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-8
                        bg-gradient-to-l from-[#0d0d0d] to-transparent z-10 hidden sm:block"></div>

        </div>
    @endif
</section>



{{-- ══════════════════════════════════════════════════════════
     AVALIAÇÕES RECENTES
══════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-14">

    <div class="flex items-center justify-between mb-5 gap-4">
        <h2 class="text-zinc-400 text-sm font-medium uppercase tracking-widest">
            Avaliações recentes
        </h2>

        @if (! $notasRecentes->isEmpty())
            <div class="flex gap-2 shrink-0">
                <button type="button"
                        data-carousel-prev="carousel-notas"
                        class="w-8 h-8 rounded-full border border-zinc-700 text-zinc-400
                               hover:border-white hover:text-white flex items-center justify-center
                               text-sm transition-colors"
                        aria-label="Anterior">
                    &#8592;
                </button>
                <button type="button"
                        data-carousel-next="carousel-notas"
                        class="w-8 h-8 rounded-full border border-zinc-700 text-zinc-400
                               hover:border-white hover:text-white flex items-center justify-center
                               text-sm transition-colors"
                        aria-label="Próximo">
                    &#8594;
                </button>
            </div>
        @endif
    </div>

    @if ($notasRecentes->isEmpty())
        <p class="text-zinc-600 text-sm">Nenhuma avaliação feita ainda.</p>
    @else
        <div class="relative group/carousel">

            {{-- Scroll de livros --}}
            <div id="carousel-notas"
                 class="flex gap-4 sm:gap-5 overflow-x-auto pb-3 scroll-smooth scrollbar-hide">

                @foreach ($notasRecentes as $nota)
                    @php $livro = $nota->livro; @endphp
                    <a href="{{ route('livros.show', $livro->google_books_id) }}"
                       class="shrink-0 w-36 sm:w-40 group block focus:outline-none focus-visible:ring-2
                              focus-visible:ring-blue-500 rounded-2xl"
                       title="{{ $livro->titulo }}">

                        <div class="aspect-[2/3] rounded-2xl overflow-hidden bg-zinc-900
                                    ring-1 ring-zinc-800/60 transition-shadow group-hover:ring-zinc-700">
                            @if ($livro->capa_livro_url)
                                <img src="{{ $livro->capa_livro_url }}"
                                     alt="{{ $livro->titulo }}"
                                     class="w-full h-full object-cover transition-transform duration-300
                                            group-hover:scale-105"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full flex items-center justify-center
                                            text-zinc-600 text-4xl">📖</div>
                            @endif
                        </div>

                        <div class="flex items-center gap-1 mt-2.5 px-0.5">
                            <svg class="w-3.5 h-3.5 text-amber-400 shrink-0"
                                 fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-zinc-300 text-xs font-medium">
                                {{ number_format($nota->nota, 1) }}
                            </span>
                        </div>
                    </a>
                @endforeach

            </div>

            {{-- Gradiente direita --}}
            <div class="pointer-events-none absolute right-0 top-0 bottom-0 w-8
                        bg-gradient-to-l from-[#0d0d0d] to-transparent z-10 hidden sm:block"></div>

        </div>
    @endif
</section>

{{-- ══════════════════════════════════════════════════════════
     ÚLTIMOS COMENTÁRIOS
══════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-20">

    <h2 class="text-zinc-400 text-sm font-medium uppercase tracking-widest mb-5">
        Últimos comentários
    </h2>

    @if ($comentariosRecentes->isEmpty())
        <p class="text-zinc-600 text-sm">Nenhum comentário feito ainda.</p>
    @else
        <div id="comentarios-lista" class="space-y-3">
            @foreach ($comentariosRecentes as $i => $comentario)
                @php
                    $livro     = $comentario->livro;
                    $notaLivro = $usuario->notas->where('id_livro', $comentario->id_livro)->first();
                @endphp
                <article class="comentario-card bg-zinc-900/70 border border-zinc-800 rounded-2xl p-4 sm:p-5
                                hover:border-zinc-700 transition-colors {{ $i >= 4 ? 'hidden' : '' }}"
                         data-page="{{ intdiv($i, 4) }}">

                    <div class="flex gap-4">

                        {{-- Capa do livro --}}
                        @if ($livro)
                            <a href="{{ route('livros.show', $livro->google_books_id) }}"
                               class="shrink-0 w-12 self-start group/cover" title="{{ $livro->titulo }}">
                                <div class="aspect-[2/3] rounded-lg overflow-hidden bg-zinc-800
                                            ring-1 ring-zinc-700/60 transition-shadow group-hover/cover:ring-zinc-600">
                                    @if ($livro->capa_livro_url)
                                        <img src="{{ $livro->capa_livro_url }}"
                                             alt="{{ $livro->titulo }}"
                                             class="w-full h-full object-cover transition-transform duration-300
                                                    group-hover/cover:scale-105"
                                             loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-zinc-600 text-lg">
                                            📖
                                        </div>
                                    @endif
                                </div>
                            </a>
                        @endif

                        {{-- Conteúdo --}}
                        <div class="flex-1 min-w-0">

                            {{-- Cabeçalho: autor + nota --}}
                            <div class="flex items-start justify-between gap-3 mb-1">
                                <div class="min-w-0">
                                    <span class="text-white font-semibold text-sm leading-tight">
                                        {{ $usuario->nome }}
                                    </span>
                                    @if ($livro)
                                        <p class="text-zinc-500 text-xs mt-0.5 truncate">
                                            {{ $livro->titulo }}
                                        </p>
                                    @endif
                                </div>

                                @if ($notaLivro)
                                    <div class="flex items-center gap-1.5 shrink-0"
                                         aria-label="Nota: {{ $notaLivro->nota }}">
                                        <x-star-display :nota="$notaLivro->nota" size="sm" />
                                        <span class="text-zinc-300 text-xs font-medium">
                                            {{ number_format($notaLivro->nota, 1) }}
                                        </span>
                                    </div>
                                @endif
                            </div>

                            {{-- Texto do comentário --}}
                            <p class="text-zinc-400 text-sm leading-relaxed line-clamp-3">
                                {{ $comentario->texto }}
                            </p>

                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        {{-- Paginação --}}
        @if ($comentariosRecentes->count() > 4)
            <div class="flex items-center justify-center gap-3 mt-7"
                 role="navigation" aria-label="Paginação de comentários">

                <button id="prev-comentario"
                        class="w-8 h-8 flex items-center justify-center rounded-full border border-zinc-700
                               text-zinc-400 hover:border-zinc-500 hover:text-white transition-colors
                               disabled:opacity-30 disabled:cursor-not-allowed"
                        aria-label="Página anterior">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>

                <div id="pagination-dots" class="flex gap-2" aria-live="polite"></div>

                <button id="next-comentario"
                        class="w-8 h-8 flex items-center justify-center rounded-full border border-zinc-700
                               text-zinc-400 hover:border-zinc-500 hover:text-white transition-colors
                               disabled:opacity-30 disabled:cursor-not-allowed"
                        aria-label="Próxima página">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>

            </div>
        @endif
    @endif

</section>

@push('scripts')
<script>
(function () {
    const cards      = [...document.querySelectorAll('.comentario-card')];
    const perPage    = 4;
    const totalPages = Math.ceil(cards.length / perPage);
    let   current    = 0;

    const dotsEl = document.getElementById('pagination-dots');
    const prevEl = document.getElementById('prev-comentario');
    const nextEl = document.getElementById('next-comentario');

    if (!dotsEl || totalPages <= 1) return;

    function show(page) {
        current = Math.max(0, Math.min(totalPages - 1, page));
        cards.forEach((card, i) => {
            card.classList.toggle('hidden', Math.floor(i / perPage) !== current);
        });
        renderDots();
        prevEl.disabled = current === 0;
        nextEl.disabled = current === totalPages - 1;
    }

    function renderDots() {
        dotsEl.innerHTML = '';
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('button');
            dot.className = [
                'w-2 h-2 rounded-full transition-all duration-200',
                i === current ? 'bg-blue-500 scale-125' : 'bg-zinc-700 hover:bg-zinc-500',
            ].join(' ');
            dot.setAttribute('aria-label', `Página ${i + 1}`);
            dot.addEventListener('click', () => show(i));
            dotsEl.appendChild(dot);
        }
    }

    prevEl.addEventListener('click', () => show(current - 1));
    nextEl.addEventListener('click', () => show(current + 1));

    show(0);
})();
</script>
@endpush

@endsection
