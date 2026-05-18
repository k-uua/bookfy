@extends('layouts.main')
@section('titulo', 'Home')

@php
$icones = [
    'star'  => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 3.679a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69L9.049 2.927z"/></svg>',
    'chat'  => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>',
    'shelf' => '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>',
];
@endphp

@section('conteudo')

{{-- ═══════════════════════════════════════════════════
     SEÇÃO 1 · Navegue pelos gêneros
════════════════════════════════════════════════════ --}}
<x-genre-carousel id="homeGenreScroll" />

{{-- ═══════════════════════════════════════════════════
     SEÇÃO 2 · Leituras Que Combinam com Você
════════════════════════════════════════════════════ --}}
@if (!empty($recomendados))
<section class="py-14 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <x-section-header
            titulo="Leituras Que Combinam com Você"
            :comNav="true"
            navTarget="recomendadosScroll"
        />

        <div id="recomendadosScroll"
             class="flex gap-4 overflow-x-auto pb-2 scroll-smooth scrollbar-hide">
            @foreach ($recomendados as $livro)
                <x-book-card :livro="$livro" :showRating="true" />
            @endforeach
        </div>

    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════
     SEÇÃO 3 · Mais bem avaliados
════════════════════════════════════════════════════ --}}
@if (!empty($melhoresAvaliados))
<section class="py-14 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <x-section-header
            titulo="Mais bem avaliados"
            :comNav="true"
            navTarget="topRatedScroll"
        />

        <div id="topRatedScroll"
             class="flex gap-4 overflow-x-auto pb-2 scroll-smooth scrollbar-hide">
            @foreach ($melhoresAvaliados as $livro)
                <x-book-card :livro="$livro" :showDate="true" />
            @endforeach
        </div>

    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════
     SEÇÃO 4 · Interações recentes
════════════════════════════════════════════════════ --}}
@if ($interacoes->isNotEmpty())
<section class="py-14 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <x-section-header
            titulo="Interações recentes"
            subtitulo="Veja o que os leitores estão lendo, avaliando e comentando"
        />

        <div class="space-y-4">
            @foreach ($interacoes as $interacao)
                @php
                    $livro   = $interacao->livro;
                    $usuario = $interacao->usuario;
                @endphp
                <div class="flex gap-5 rounded-2xl bg-[#161616] border border-zinc-800/60 p-5
                            hover:border-zinc-600 transition-colors group">

                    <a href="{{ $livro->google_books_id ? route('livros.show', $livro->google_books_id) : '#' }}"
                       class="shrink-0">
                        @if ($livro->capa_livro_url)
                            <img src="{{ $livro->capa_livro_url }}"
                                 alt="{{ $livro->titulo }}"
                                 class="w-16 h-24 sm:w-20 sm:h-28 object-cover rounded-xl shadow-md">
                        @else
                            <div class="w-16 h-24 sm:w-20 sm:h-28 rounded-xl bg-zinc-800
                                        flex items-center justify-center text-zinc-600 text-2xl">
                                📖
                            </div>
                        @endif
                    </a>

                    <div class="flex-1 min-w-0">
                        <a href="{{ $livro->google_books_id ? route('livros.show', $livro->google_books_id) : '#' }}"
                           class="block font-semibold text-white text-sm sm:text-base
                                  hover:text-blue-400 transition-colors truncate mb-1">
                            {{ $livro->titulo }}
                        </a>

                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <div class="flex items-center gap-1.5">
                                <div class="flex items-center gap-1.5">
                                    <x-avatar :usuario="$usuario" size="xs" class="bg-blue-600" />
                                    <span class="text-zinc-400 text-xs">{{ $usuario->nome ?? 'Usuário' }}</span>
                                </div>
                            </div>

                            @if ($interacao->notaUsuario)
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg class="w-3.5 h-3.5 {{ $i <= $interacao->notaUsuario->nota ? 'text-amber-400' : 'text-zinc-700' }}"
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                    @endfor
                                </div>
                            @endif
                        </div>

                        <p class="text-zinc-400 text-sm leading-relaxed line-clamp-2">
                            {{ $interacao->texto }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════
     SEÇÃO 5 · Painel de progresso
════════════════════════════════════════════════════ --}}
<section class="py-14 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <x-section-header
            titulo="Painel de progresso"
            subtitulo="Ganhe medalhas avaliando, comentando e organizando livros"
        />

        <div class="space-y-4">
            @foreach ($progresso as $conquista)
                <div class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-5">

                    <div class="flex items-center justify-between mb-3 gap-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-full bg-zinc-800 flex items-center
                                        justify-center text-zinc-400 shrink-0">
                                {!! $icones[$conquista['icone']] ?? $icones['star'] !!}
                            </div>
                            <div class="min-w-0">
                                <p class="font-semibold text-white text-sm truncate">
                                    {{ $conquista['titulo'] }}
                                </p>
                                <p class="text-zinc-500 text-xs">
                                    {{ $conquista['descricao'] }}
                                </p>
                            </div>
                        </div>

                        <span class="text-zinc-300 text-sm font-medium shrink-0">
                            {{ $conquista['percentual'] }}%
                        </span>
                    </div>

                    <div class="h-2 bg-zinc-800 rounded-full overflow-hidden"
                         role="progressbar"
                         aria-valuenow="{{ $conquista['percentual'] }}"
                         aria-valuemin="0"
                         aria-valuemax="100">
                        <div class="h-full bg-blue-600 transition-all duration-500"
                             style="width: {{ $conquista['percentual'] }}%"></div>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
</section>

@endsection
