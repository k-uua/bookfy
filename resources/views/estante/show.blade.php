@extends('layouts.main')
@section('titulo', $estante->nome)

@php
    $statusBadge = [
        'quero_ler' => ['label' => 'Quero ler', 'classe' => 'bg-zinc-700 text-zinc-200'],
        'lendo'     => ['label' => 'Lendo',     'classe' => 'bg-blue-600 text-white'],
        'lido'      => ['label' => 'Lido',      'classe' => 'bg-emerald-600 text-white'],
    ];
@endphp

@section('conteudo')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Cabeçalho --}}
    <div class="flex items-start gap-4 mb-8 flex-wrap">
        <a href="{{ route('estante.index') }}"
           class="flex items-center gap-1.5 text-zinc-400 hover:text-white text-sm
                  transition-colors shrink-0 mt-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>

        <div class="flex-1 min-w-0">
            <h1 class="text-2xl font-bold text-white truncate">{{ $estante->nome }}</h1>
            <p class="text-zinc-500 text-sm mt-1">
                {{ $livros->count() }} {{ Str::plural('livro', $livros->count()) }}
            </p>
        </div>
    </div>

    @if ($livros->isEmpty())

        {{-- ──── Estado vazio ──── --}}
        <div class="text-center py-20">
            <div class="w-16 h-16 rounded-full bg-zinc-800 flex items-center justify-center mx-auto mb-4 text-3xl">
                📖
            </div>
            <p class="text-zinc-200 text-lg font-medium mb-1">Esta estante está vazia</p>
            <p class="text-zinc-500 text-sm mb-6">
                Comece adicionando livros para vê-los aqui.
            </p>
            <a href="{{ route('home') }}"
               class="inline-block bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold
                      px-6 py-2.5 rounded-lg transition-colors shadow-lg shadow-blue-900/30">
                Adicionar livros
            </a>
        </div>

    @else

        {{-- ──── Grid de livros ──── --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3 sm:gap-4">
            @foreach ($livros as $livro)
                @php
                    $status = $livro->pivot->status ?? 'quero_ler';
                    $badge  = $statusBadge[$status] ?? $statusBadge['quero_ler'];
                @endphp

                <a href="{{ route('livros.show', $livro->google_books_id) }}"
                   class="group flex flex-col bg-[#161616] hover:bg-[#1a1a1a]
                          border border-zinc-800/60 hover:border-zinc-700
                          rounded-2xl overflow-hidden transition-colors">

                    {{-- Capa --}}
                    <div class="aspect-[2/3] bg-zinc-900 overflow-hidden relative">
                        @if ($livro->capa_livro_url)
                            <img src="{{ $livro->capa_livro_url }}"
                                 alt="Capa de {{ $livro->titulo }}"
                                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                                 loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-zinc-700 text-4xl">
                                📖
                            </div>
                        @endif

                        {{-- Star de favorito sobreposta --}}
                        @if ($livro->pivot->favorito)
                            <span class="absolute top-2 right-2 text-amber-400 drop-shadow-lg" title="Favorito">
                                ⭐
                            </span>
                        @endif
                    </div>

                    {{-- Info --}}
                    <div class="p-3 flex-1 flex flex-col gap-1.5">
                        <h3 class="font-semibold text-white text-[13px] leading-snug line-clamp-2
                                   group-hover:text-blue-400 transition-colors">
                            {{ $livro->titulo }}
                        </h3>

                        <div class="mt-auto">
                            <span class="inline-block text-[11px] font-medium px-2 py-0.5 rounded-full {{ $badge['classe'] }}">
                                {{ $badge['label'] }}
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

    @endif

</div>

@endsection
