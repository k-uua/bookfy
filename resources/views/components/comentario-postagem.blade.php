@props([
    'comentario',
    'nivel' => 0,   // profundidade da thread (limita indentação visual)
])

<div class="space-y-3">

    <article class="bg-[#0d0d0d] border border-zinc-800/60 rounded-xl p-4">

        {{-- Cabeçalho: autor + data + delete --}}
        <header class="flex items-start justify-between gap-3 mb-2">
            <div class="flex items-center gap-2 min-w-0">
                <x-avatar :usuario="$comentario->usuario" size="sm" class="bg-blue-600" />
                <div class="min-w-0">
                    <p class="text-white text-xs font-semibold truncate">
                        {{ $comentario->usuario->nome ?? 'Usuário' }}
                    </p>
                    <p class="text-zinc-500 text-[11px]">
                        {{ $comentario->criado_em->diffForHumans() }}
                    </p>
                </div>
            </div>

            @auth
                @if (Auth::id() === $comentario->id_usuario)
                    <form action="{{ route('postagens.comentarios.destroy', $comentario) }}"
                          method="post" class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                onclick="return confirm('Remover este comentário?')"
                                class="text-red-400 hover:text-red-300 text-[11px] transition-colors">
                            Remover
                        </button>
                    </form>
                @endif
            @endauth
        </header>

        {{-- Texto --}}
        <p class="text-zinc-300 text-sm leading-relaxed whitespace-pre-line mb-3">
            {{ $comentario->texto }}
        </p>

        {{-- Ações (responder) --}}
        @auth
            <button type="button" data-toggle="responder-{{ $comentario->id }}"
                    class="text-zinc-400 hover:text-white text-xs transition-colors">
                ↩ Responder
            </button>

            {{-- Form de resposta (oculto até clicar) --}}
            <div id="responder-{{ $comentario->id }}" class="hidden mt-3">
                <form action="{{ route('postagens.comentarios.responder', $comentario) }}" method="post">
                    @csrf
                    <textarea name="texto" rows="2" maxlength="1000" minlength="3" required
                              placeholder="Escreva sua resposta..."
                              class="w-full bg-[#161616] border border-zinc-700 rounded-lg px-3 py-2
                                     text-xs text-white placeholder-zinc-500 outline-none resize-none
                                     focus:border-blue-500 focus:ring-1 focus:ring-blue-500/40 transition-colors"></textarea>
                    <div class="flex gap-2 mt-2">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-500 text-white text-xs font-medium
                                       px-3 py-1.5 rounded-md transition-colors">
                            Publicar resposta
                        </button>
                        <button type="button" data-toggle="responder-{{ $comentario->id }}"
                                class="text-zinc-400 hover:text-white text-xs transition-colors px-3 py-1.5">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        @endauth
    </article>

    {{-- Respostas filhas (threading com indentação limitada) --}}
    @if ($comentario->respostas->isNotEmpty() && $nivel < 3)
        <div class="ml-4 sm:ml-6 pl-4 border-l-2 border-zinc-800 space-y-3">
            @foreach ($comentario->respostas->sortBy('criado_em') as $resposta)
                <x-comentario-postagem :comentario="$resposta" :nivel="$nivel + 1" />
            @endforeach
        </div>
    @endif

</div>
