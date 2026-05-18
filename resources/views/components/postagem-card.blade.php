@props([
    'postagem',
    'curtidaPeloUsuario' => false,
    'compacto'           => true,    // se true: trunca conteúdo no feed
])

@php
    $usuario   = $postagem->usuario;
    $livro     = $postagem->livro;
    $likes     = $postagem->likes_count   ?? 0;
    $comments  = $postagem->comentarios_count ?? 0;
@endphp

<article class="bg-[#161616] border border-zinc-800/60 hover:border-zinc-700
                rounded-2xl p-5 sm:p-6 transition-colors">

    {{-- Cabeçalho: autor + data --}}
    <header class="flex items-center justify-between gap-3 mb-3">
        <div class="flex items-center gap-2.5 min-w-0">
            <x-avatar :usuario="$usuario" size="lg" class="bg-blue-600" />
            <div class="min-w-0">
                <p class="text-white text-sm font-semibold truncate">
                    {{ $usuario->nome ?? 'Usuário' }}
                </p>
                <p class="text-zinc-500 text-xs">
                    {{ $postagem->criado_em->diffForHumans() }}
                </p>
            </div>
        </div>

        {{-- Menu de deletar (só para o autor) --}}
        @auth
            @if (Auth::id() === $postagem->id_usuario)
                <form action="{{ route('postagens.destroy', $postagem) }}" method="post" class="shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            onclick="return confirm('Remover esta postagem?')"
                            class="text-zinc-500 hover:text-red-400 text-xs transition-colors px-2 py-1">
                        Remover
                    </button>
                </form>
            @endif
        @endauth
    </header>

    {{-- Título da postagem (link para a página completa) --}}
    <a href="{{ route('postagens.show', $postagem) }}" class="block group">
        <h2 class="text-white font-bold text-lg sm:text-xl leading-snug mb-2
                   group-hover:text-blue-400 transition-colors">
            {{ $postagem->titulo }}
        </h2>
    </a>

    {{-- Conteúdo (trunca no feed, mostra completo no show) --}}
    <p @class([
        'text-zinc-300 text-sm leading-relaxed whitespace-pre-line mb-4',
        'line-clamp-4' => $compacto,
    ])>{{ $postagem->conteudo }}</p>

    {{-- Livro relacionado (se houver) --}}
    @if ($livro)
        <a href="{{ route('livros.show', $livro->google_books_id ?? $livro->id) }}"
           class="flex items-center gap-3 bg-[#0d0d0d] border border-zinc-800/60
                  hover:border-zinc-700 rounded-xl p-3 mb-4 transition-colors group">
            @if ($livro->capa_livro_url)
                <img src="{{ $livro->capa_livro_url }}"
                     alt="{{ $livro->titulo }}"
                     class="w-12 h-16 object-cover rounded-md shrink-0 shadow-md">
            @else
                <div class="w-12 h-16 rounded-md bg-zinc-800 flex items-center justify-center
                            text-zinc-600 text-xl shrink-0">📖</div>
            @endif
            <div class="flex-1 min-w-0">
                <p class="text-[10px] uppercase tracking-wider text-zinc-500 mb-0.5">Livro</p>
                <p class="text-white text-sm font-semibold truncate group-hover:text-blue-400 transition-colors">
                    {{ $livro->titulo }}
                </p>
            </div>
            <svg class="w-4 h-4 text-zinc-500 group-hover:text-blue-400 transition-colors shrink-0"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </a>
    @endif

    {{-- Footer: ações sociais --}}
    <footer class="flex items-center gap-1 pt-3 border-t border-zinc-800/60">

        {{-- Like --}}
        @auth
            <form action="{{ route('postagens.like', $postagem) }}" method="post">
                @csrf
                <button type="submit"
                        @class([
                            'flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm transition-colors',
                            'text-red-400 hover:bg-red-950/40' => $curtidaPeloUsuario,
                            'text-zinc-400 hover:text-red-400 hover:bg-zinc-800' => !$curtidaPeloUsuario,
                        ])>
                    <svg class="w-4 h-4" fill="{{ $curtidaPeloUsuario ? 'currentColor' : 'none' }}"
                         stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span class="tabular-nums">{{ $likes }}</span>
                </button>
            </form>
        @else
            <a href="{{ route('usuario.login') }}"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm
                      text-zinc-400 hover:text-red-400 hover:bg-zinc-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <span class="tabular-nums">{{ $likes }}</span>
            </a>
        @endauth

        {{-- Comentários --}}
        <a href="{{ route('postagens.show', $postagem) }}"
           class="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm
                  text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span class="tabular-nums">{{ $comments }}</span>
        </a>
    </footer>
</article>
