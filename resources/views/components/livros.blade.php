@extends('layouts.main')
@section('titulo', $titulo ?? 'Resultados')

@section('conteudo')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    @if ($livros->isEmpty())

        {{-- ───────────────────────────────────────────
             Estado vazio
        ──────────────────────────────────────────── --}}
        <div class="text-center py-20">
            <div class="w-16 h-16 rounded-full bg-zinc-800 flex items-center justify-center mx-auto mb-4 text-3xl">
                🔍
            </div>
            <p class="text-zinc-200 text-lg font-medium mb-1">Nenhum livro encontrado</p>
            <p class="text-zinc-500 text-sm mb-6">
                Tente uma busca diferente ou explore por categoria.
            </p>
            <a href="{{ route('livros.index') }}"
               class="inline-block bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold
                      px-6 py-2.5 rounded-lg transition-colors shadow-lg shadow-blue-900/30">
                Voltar ao início
            </a>
        </div>

    @else

        {{-- Cabeçalho com contagem --}}
        <div class="mb-6 flex flex-wrap items-end justify-between gap-2">
            <h1 class="text-2xl font-bold text-white">
                {{ $titulo ?? 'Resultados' }}
            </h1>
            <p class="text-zinc-500 text-sm">
                {{ number_format($livros->total()) }} {{ Str::plural('livro', $livros->total()) }} encontrados
            </p>
        </div>

        {{-- ───────────────────────────────────────────
             Grid de livros — 5 por linha em telas md+
        ──────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 sm:gap-4">
            @foreach ($livros as $livro)
                @php
                    $info    = $livro['volumeInfo'] ?? [];
                    $tituloL = $info['title'] ?? 'Sem título';
                    $autorL  = $info['authors'][0] ?? 'Autor desconhecido';
                    $capaL   = str_replace('http://', 'https://',
                        $info['imageLinks']['thumbnail']
                        ?? $info['imageLinks']['smallThumbnail']
                        ?? '');
                    $notaL   = $info['averageRating'] ?? null;
                @endphp

                <a href="{{ route('livros.show', $livro['id']) }}"
                   class="group flex flex-col bg-[#161616] hover:bg-[#1a1a1a]
                          border border-zinc-800/60 hover:border-zinc-700
                          rounded-2xl overflow-hidden transition-colors">

                    {{-- Capa --}}
                    <div class="aspect-[2/3] bg-zinc-900 overflow-hidden relative">
                        @if ($capaL)
                            <img src="{{ $capaL }}"
                                 alt="Capa de {{ $tituloL }}"
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                 loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-zinc-700 text-4xl">
                                📖
                            </div>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-3 flex-1 flex flex-col gap-0.5">
                        <h3 class="font-semibold text-white text-[13px] leading-snug line-clamp-2
                                   group-hover:text-blue-400 transition-colors">
                            {{ $tituloL }}
                        </h3>
                        <p class="text-zinc-500 text-[11px] truncate">{{ $autorL }}</p>

                        @if ($notaL)
                            <div class="flex items-center gap-1 mt-1">
                                <x-star-display :nota="$notaL" size="sm" />
                                <span class="text-zinc-400 text-[11px]">{{ number_format($notaL, 1) }}</span>
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        {{-- ───────────────────────────────────────────
             Paginação
        ──────────────────────────────────────────── --}}
        @if ($livros->hasPages())
            <nav class="flex items-center justify-center gap-2 mt-10" aria-label="Paginação">

                {{-- Anterior --}}
                @if ($livros->onFirstPage())
                    <span class="flex items-center gap-1.5 px-4 py-2 text-sm text-zinc-700 cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Anterior
                    </span>
                @else
                    <a href="{{ $livros->previousPageUrl() }}"
                       class="flex items-center gap-1.5 px-4 py-2 text-sm text-zinc-300
                              hover:text-white hover:bg-zinc-800 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Anterior
                    </a>
                @endif

                {{-- Indicador da página atual --}}
                <span class="px-4 py-2 text-sm text-zinc-400">
                    Página
                    <span class="text-white font-semibold">{{ $livros->currentPage() }}</span>
                    de {{ $livros->lastPage() }}
                </span>

                {{-- Próxima --}}
                @if ($livros->hasMorePages())
                    <a href="{{ $livros->nextPageUrl() }}"
                       class="flex items-center gap-1.5 px-4 py-2 text-sm text-zinc-300
                              hover:text-white hover:bg-zinc-800 rounded-lg transition-colors">
                        Próxima
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                @else
                    <span class="flex items-center gap-1.5 px-4 py-2 text-sm text-zinc-700 cursor-not-allowed">
                        Próxima
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </span>
                @endif

            </nav>
        @endif

    @endif

</div>

@endsection
