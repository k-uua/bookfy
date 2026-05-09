@extends('layouts.main')
@section('titulo', 'Início')

@php
$forunsPlaceholder = [
    ['titulo' => 'Indicações de livros para começar a ler mais',     'descricao' => 'Está começando a criar o hábito da leitura? Compartilhe e descubra livros, acessórios e ideias para os que estão iniciando nessa jornada.', 'membros' => 15],
    ['titulo' => 'Livros parecidos com Harry Potter',                 'descricao' => 'Procurando histórias com magia, aventura e mundos fantásticos como Harry Potter? Compartilhe e descubra novas leituras nesse estilo.',       'membros' => 5],
    ['titulo' => 'Livros que te prenderam do início ao fim',          'descricao' => 'Está começando a criar o hábito da leitura? Compartilhe e descubra livros, acessórios e ideias para dar um primeiro passo.',                   'membros' => 10],
    ['titulo' => 'Livros que mudaram sua perspectiva de vida',        'descricao' => 'Compartilhe aqueles livros que fizeram você pensar diferente e ver o mundo com outros olhos.',                                                  'membros' => 23],
];
@endphp

@section('conteudo')

{{-- ═══════════════════════════════════════════════════
     SEÇÃO 1 · Hero — mosaico de capas + headline
════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden" style="min-height: 88vh;">

    @if (!empty($capas))
        <div class="absolute inset-0 grid gap-0.5 overflow-hidden pointer-events-none select-none"
             style="grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));">
            @foreach (array_merge($capas, $capas) as $capa)
                <img src="{{ $capa }}" alt="" class="w-full object-cover"
                     style="height: 160px;" loading="lazy">
            @endforeach
        </div>
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-950"></div>
    @endif

    <div class="absolute inset-0"
         style="background: linear-gradient(
             to bottom,
             rgba(13,13,13,0.45) 0%,
             rgba(13,13,13,0.65) 40%,
             rgba(13,13,13,0.90) 75%,
             rgba(13,13,13,1)    100%
         );"></div>

    <div class="relative z-10 flex flex-col items-center justify-center text-center px-4"
         style="min-height: 88vh;">
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold text-white leading-tight max-w-2xl">
            Descubra, compartilhe e<br>
            conecte-se com outros leitores
        </h1>

        {{-- Página de apresentação: qualquer ação leva para login --}}
        <a href="{{ route('usuario.login') }}"
           class="mt-8 inline-block bg-blue-600 hover:bg-blue-500 active:bg-blue-700
                  text-white font-semibold text-sm px-8 py-3 rounded-md transition-colors
                  shadow-lg shadow-blue-900/40">
            Comece agora
        </a>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════
     SEÇÃO 2 · Navegue pelos gêneros (modo guest)
════════════════════════════════════════════════════ --}}
<x-genre-carousel guest id="genreScroll" />

{{-- ═══════════════════════════════════════════════════
     SEÇÃO 3 · Interações recentes (preview, sem clique)
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
                <a href="{{ route('usuario.login') }}"
                   class="flex gap-5 rounded-2xl bg-[#161616] border border-zinc-800/60 p-5
                          hover:border-zinc-600 transition-colors group">

                    <div class="shrink-0">
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
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white text-sm sm:text-base group-hover:text-blue-400
                                  transition-colors truncate mb-1">
                            {{ $livro->titulo }}
                        </p>

                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full bg-blue-600 flex items-center justify-center
                                            text-white font-bold text-[10px] shrink-0">
                                    {{ mb_strtoupper(mb_substr($usuario->nome ?? '?', 0, 1)) }}
                                </div>
                                <span class="text-zinc-400 text-xs">{{ $usuario->nome ?? 'Usuário' }}</span>
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
                </a>
            @endforeach
        </div>

    </div>
</section>
@endif

{{-- ═══════════════════════════════════════════════════
     SEÇÃO 4 · Fóruns da comunidade (preview)
════════════════════════════════════════════════════ --}}
<section class="py-14 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <x-section-header
            titulo="Fóruns da comunidade"
            subtitulo="Participe de discussões, tire dúvidas e compartilhe suas opiniões sobre livros."
        />

        <div class="space-y-3">
            @foreach ($forunsPlaceholder as $forum)
                <a href="{{ route('usuario.login') }}"
                   class="flex items-center gap-4 rounded-2xl bg-[#161616] border border-zinc-800/60 px-5 py-4
                          hover:border-zinc-600 transition-colors group">

                    <div class="shrink-0 w-9 h-9 rounded-full bg-zinc-800 flex items-center justify-center
                                group-hover:bg-zinc-700 transition-colors">
                        <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-white text-sm truncate mb-0.5 group-hover:text-blue-400 transition-colors">
                            {{ $forum['titulo'] }}
                        </p>
                        <p class="text-zinc-500 text-xs leading-relaxed line-clamp-1">
                            {{ $forum['descricao'] }}
                        </p>
                    </div>

                    <div class="shrink-0 flex items-center gap-1.5 text-zinc-400 text-xs ml-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>{{ $forum['membros'] }} Membros</span>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
</section>

@endsection
