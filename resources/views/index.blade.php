@extends('layouts.main')
@section('titulo', 'Início')

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
                                <x-avatar :usuario="$usuario" size="xs" class="bg-blue-600" />
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
     SEÇÃO 4 · Postagens em destaque (preview do feed)
════════════════════════════════════════════════════ --}}
@if ($postagens->isNotEmpty())
<section class="py-14 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <x-section-header
            titulo="Em alta na comunidade"
            subtitulo="Postagens populares do feed do Bookfy — entre para participar."
        />

        <div class="space-y-3 mx-auto">
            @foreach ($postagens as $postagem)
                <a href="{{ route('usuario.login') }}"
                   class="block bg-[#161616] border border-zinc-800/60 hover:border-zinc-700
                          rounded-2xl p-5 transition-colors group">

                    {{-- Autor + data --}}
                    <div class="flex items-center gap-2 mb-3">
                        <x-avatar :usuario="$postagem->usuario" size="sm" class="bg-blue-600" />
                        <span class="text-zinc-400 text-xs">
                            {{ $postagem->usuario->nome ?? 'Usuário' }} ·
                            {{ $postagem->criado_em->diffForHumans() }}
                        </span>
                    </div>

                    {{-- Título + conteúdo --}}
                    <h3 class="text-white font-bold text-base sm:text-lg leading-snug mb-2
                               group-hover:text-blue-400 transition-colors line-clamp-2">
                        {{ $postagem->titulo }}
                    </h3>
                    <p class="text-zinc-400 text-sm leading-relaxed line-clamp-2 mb-3">
                        {{ $postagem->conteudo }}
                    </p>

                    {{-- Métricas sociais --}}
                    <div class="flex items-center gap-4 text-zinc-500 text-xs">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                            {{ $postagem->likes_count }} {{ Str::plural('curtida', $postagem->likes_count) }}
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            {{ $postagem->comentarios_count }} {{ Str::plural('comentário', $postagem->comentarios_count) }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

    </div>
</section>
@endif

@endsection
