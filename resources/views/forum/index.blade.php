@extends('layouts.main')
@section('titulo', 'Fóruns')

@php
$acoes = [
    [
        'titulo' => 'Criar fórum',
        'modal'  => 'criarForumModal',  // abre modal em vez de navegar
        'imagem' => asset('images/IMAGEM%201.png'),
    ],
    [
        'titulo' => 'Seus fóruns',
        'href'   => '#',  // TODO: filtro "meus"
        'imagem' => asset('images/IMAGEM%202.png'),
    ],
    [
        'titulo' => 'Explorar',
        'href'   => '#',  // TODO: lista completa
        'imagem' => asset('images/IMAGEM%203.png'),
    ],
];
@endphp

@section('conteudo')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

    {{-- ═══════════════════════════════════════════════════
         Top · 3 cards de ação
    ════════════════════════════════════════════════════ --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @foreach ($acoes as $acao)
            @php
                // Card pode ser link (href) ou botão que abre modal (modal).
                $tag         = isset($acao['modal']) ? 'button' : 'a';
                $attrs       = isset($acao['modal'])
                    ? 'type="button" data-modal-open="' . $acao['modal'] . '"'
                    : 'href="' . $acao['href'] . '"';
                $classesBase = 'group block w-full text-left';
            @endphp

            <{{ $tag }} {!! $attrs !!} class="{{ $classesBase }}">
                <h2 class="text-center text-white text-xl font-bold mb-4
                           group-hover:text-blue-400 transition-colors">
                    {{ $acao['titulo'] }}
                </h2>
                <div class="aspect-[16/10] rounded-2xl overflow-hidden
                            shadow-lg shadow-blue-900/30 transition-transform
                            duration-300 group-hover:scale-[1.02]">
                    <img src="{{ $acao['imagem'] }}"
                         alt="{{ $acao['titulo'] }}"
                         class="w-full h-full object-cover">
                </div>
            </{{ $tag }}>
        @endforeach
    </section>

    {{-- ═══════════════════════════════════════════════════
         Fóruns em alta
    ════════════════════════════════════════════════════ --}}
    <section class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-6 sm:p-8">

        <h2 class="text-base font-semibold text-white mb-6">Fóruns em alta</h2>

        @if ($forunsEmAlta->isEmpty())
            <div class="text-center py-12">
                <div class="w-14 h-14 rounded-full bg-zinc-800 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <p class="text-zinc-300 text-sm font-medium">Ainda não há fóruns por aqui</p>
                <p class="text-zinc-500 text-xs mt-1">Seja o primeiro a criar um e começar uma discussão.</p>
            </div>
        @else
            <div class="divide-y divide-zinc-800/60">
                @foreach ($forunsEmAlta as $i => $forum)
                    <a href="{{ route('forum.show', $forum) }}"
                       class="flex items-center gap-4 sm:gap-5 py-5 group first:pt-0 last:pb-0">

                        {{-- Número --}}
                        <span class="text-zinc-500 text-lg font-medium tabular-nums w-7 text-right shrink-0">
                            {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>

                        {{-- Ícone --}}
                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl bg-blue-500
                                    flex items-center justify-center shrink-0
                                    group-hover:bg-blue-400 transition-colors">
                            <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor"
                                 stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                        </div>

                        {{-- Conteúdo --}}
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-white text-sm sm:text-base
                                       group-hover:text-blue-400 transition-colors truncate">
                                {{ $forum->nome }}
                            </h3>
                            <p class="text-zinc-400 text-xs sm:text-sm line-clamp-2 mt-1">
                                {{ $forum->descricao }}
                            </p>
                        </div>

                        {{-- Métrica · contagem de tópicos --}}
                        <span class="flex items-center gap-1.5 text-zinc-400 text-xs shrink-0
                                     bg-zinc-800/60 border border-zinc-800 rounded-full px-3 py-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <span class="whitespace-nowrap">
                                {{ $forum->topicos_count }} {{ Str::plural('tópico', $forum->topicos_count) }}
                            </span>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

</div>

{{-- Modal de criação do fórum (disparado pelo card "Criar fórum") --}}
<x-modal-criar-forum id="criarForumModal" />

@endsection
