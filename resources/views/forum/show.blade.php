@extends('layouts.main')
@section('titulo', $forum->nome)
@section('conteudo')

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

    {{-- Link de volta --}}
    <a href="{{ route('forum.index') }}"
       class="inline-flex items-center gap-1.5 text-zinc-400 hover:text-white
              text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar para fóruns
    </a>

    {{-- ═══════════════════════════════════════════════════════════════
         Cabeçalho do fórum
    ════════════════════════════════════════════════════════════════ --}}
    <header class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-6 sm:p-8">

        <div class="flex items-start gap-4 mb-4">
            {{-- Ícone do fórum --}}
            <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-xl bg-blue-500
                        flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor"
                     stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>

            <div class="flex-1 min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-white leading-tight">
                    {{ $forum->nome }}
                </h1>
            </div>
        </div>

        <p class="text-zinc-300 text-sm sm:text-base leading-relaxed mb-5 whitespace-pre-line">
            {{ $forum->descricao }}
        </p>

        {{-- Metadados --}}
        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 pt-4 border-t border-zinc-800/60">

            {{-- Criador --}}
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-blue-600 flex items-center justify-center
                            text-white font-bold text-[10px] shrink-0">
                    {{ mb_strtoupper(mb_substr($forum->usuario->nome ?? '?', 0, 1)) }}
                </div>
                <span class="text-zinc-400 text-xs">
                    Criado por
                    <strong class="text-zinc-200 font-medium">{{ $forum->usuario->nome ?? 'Usuário' }}</strong>
                </span>
            </div>

            {{-- Data --}}
            <span class="text-zinc-500 text-xs">•</span>
            <span class="text-zinc-400 text-xs">{{ $forum->criado_em->diffForHumans() }}</span>

            {{-- Contagem de tópicos --}}
            <span class="text-zinc-500 text-xs">•</span>
            <span class="text-zinc-400 text-xs">
                {{ $topicos->count() }} {{ Str::plural('tópico', $topicos->count()) }}
            </span>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════════════════════════
         Lista de tópicos
    ════════════════════════════════════════════════════════════════ --}}
    <section class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-6 sm:p-8">

        <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
            <h2 class="text-base font-semibold text-white">Tópicos</h2>

            <button type="button" data-modal-open="criarTopicoModal"
                    class="flex items-center gap-1.5 bg-blue-600 hover:bg-blue-500 active:bg-blue-700
                           text-white text-sm font-semibold px-4 py-2 rounded-lg
                           shadow-lg shadow-blue-900/30 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Novo tópico
            </button>
        </div>

        @if ($topicos->isEmpty())

            {{-- ──── Estado vazio ──── --}}
            <div class="text-center py-12">
                <div class="w-14 h-14 rounded-full bg-zinc-800 flex items-center
                            justify-center mx-auto mb-3 text-2xl">
                    💭
                </div>
                <p class="text-zinc-200 text-sm font-medium mb-1">Ainda não há tópicos</p>
                <p class="text-zinc-500 text-xs mb-5">
                    Seja o primeiro a iniciar uma discussão neste fórum.
                </p>
                <button type="button" data-modal-open="criarTopicoModal"
                        class="inline-flex items-center gap-1.5 bg-blue-600 hover:bg-blue-500
                               text-white text-sm font-semibold px-5 py-2 rounded-lg
                               transition-colors shadow-lg shadow-blue-900/30">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                    </svg>
                    Criar primeiro tópico
                </button>
            </div>

        @else

            {{-- ──── Lista de tópicos ──── --}}
            <div class="divide-y divide-zinc-800/60">
                @foreach ($topicos as $topico)
                    <a href="#"
                       class="flex items-start gap-4 py-5 group first:pt-0 last:pb-0">

                        {{-- Avatar do criador --}}
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center
                                    text-white font-bold text-sm shrink-0">
                            {{ mb_strtoupper(mb_substr($topico->usuario->nome ?? '?', 0, 1)) }}
                        </div>

                        {{-- Conteúdo --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-white text-sm sm:text-base
                                       group-hover:text-blue-400 transition-colors line-clamp-2">
                                {{ $topico->titulo }}
                            </h3>
                            <p class="text-zinc-500 text-xs mt-1">
                                Por
                                <span class="text-zinc-400">{{ $topico->usuario->nome ?? 'Usuário' }}</span>
                                · {{ $topico->criado_em->diffForHumans() }}
                            </p>
                        </div>

                        {{-- Métrica · contagem de comentários --}}
                        <span class="flex items-center gap-1.5 text-zinc-400 text-xs shrink-0
                                     bg-zinc-800/60 border border-zinc-800 rounded-full px-3 py-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span class="whitespace-nowrap">
                                {{ $topico->comentarios_count }}
                                {{ Str::plural('comentário', $topico->comentarios_count) }}
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>

        @endif
    </section>

</div>

{{-- Modal · Criar tópico --}}
<x-modal-criar-topico id="criarTopicoModal" :forum="$forum" />

@endsection
