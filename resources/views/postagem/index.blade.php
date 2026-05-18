@extends('layouts.main')
@section('titulo', 'Feed')
@section('conteudo')

<div class="max-w-6xl mx-auto px-4 sm:px-6 py-8">
    <div class="flex gap-8 items-start">

        {{-- ── Coluna principal (feed) ───────────────────────────── --}}
        <div class="flex-1 min-w-0 space-y-6">

            {{-- Cabeçalho do feed --}}
            <header class="flex items-center justify-between gap-3 flex-wrap">
                <div>
                    <h1 class="text-2xl font-bold text-white">Feed</h1>
                    <p class="text-zinc-500 text-sm mt-1">
                        Descubra o que a comunidade está lendo, pensando e recomendando.
                    </p>
                </div>

                <button type="button" data-modal-open="criarPostagemModal"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700
                               text-white text-sm font-semibold px-4 py-2.5 rounded-lg
                               shadow-lg shadow-blue-900/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nova postagem
                </button>
            </header>

            @if ($postagens->isEmpty())

                {{-- ──── Estado vazio ──── --}}
                <div class="text-center py-16 bg-[#161616] border border-zinc-800/60 rounded-2xl">
                    <div class="w-16 h-16 rounded-full bg-zinc-800 flex items-center justify-center mx-auto mb-4 text-3xl">
                        ✍️
                    </div>
                    <p class="text-zinc-200 text-lg font-medium mb-1">O feed está vazio</p>
                    <p class="text-zinc-500 text-sm mb-6">
                        Seja o primeiro a publicar algo para a comunidade.
                    </p>
                    <button type="button" data-modal-open="criarPostagemModal"
                            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500
                                   text-white text-sm font-semibold px-6 py-2.5 rounded-lg
                                   transition-colors shadow-lg shadow-blue-900/30">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                        </svg>
                        Criar primeira postagem
                    </button>
                </div>

            @else

                {{-- ──── Lista de postagens ──── --}}
                <div class="space-y-4">
                    @foreach ($postagens as $postagem)
                        <x-postagem-card
                            :postagem="$postagem"
                            :curtidaPeloUsuario="Auth::check() && $postagem->curtidoPor(Auth::id())"
                            :compacto="true"
                        />
                    @endforeach
                </div>

                {{-- ──── Paginação ──── --}}
                @if ($postagens->hasPages())
                    <nav class="flex items-center justify-center gap-2 pt-4" aria-label="Paginação">
                        @if ($postagens->onFirstPage())
                            <span class="flex items-center gap-1.5 px-4 py-2 text-sm text-zinc-700 cursor-not-allowed">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Anterior
                            </span>
                        @else
                            <a href="{{ $postagens->previousPageUrl() }}"
                               class="flex items-center gap-1.5 px-4 py-2 text-sm text-zinc-300
                                      hover:text-white hover:bg-zinc-800 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                                Anterior
                            </a>
                        @endif

                        <span class="px-4 py-2 text-sm text-zinc-400">
                            Página <span class="text-white font-semibold">{{ $postagens->currentPage() }}</span>
                            de {{ $postagens->lastPage() }}
                        </span>

                        @if ($postagens->hasMorePages())
                            <a href="{{ $postagens->nextPageUrl() }}"
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

        {{-- ── Sidebar (oculta em mobile) ────────────────────────── --}}
        <div class="hidden lg:block w-72 shrink-0 sticky top-24">
            <x-atividade-sidebar :topUsuarios="$topUsuarios" :topPostagens="$topPostagens" />
        </div>

    </div>
</div>

{{-- Modal de criação --}}
<x-modal-criar-postagem id="criarPostagemModal" />

@endsection
