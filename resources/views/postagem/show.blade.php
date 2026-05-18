@extends('layouts.main')
@section('titulo', $postagem->titulo)
@section('conteudo')

<div class="max-w-2xl mx-auto px-4 sm:px-6 py-8 space-y-6">

    {{-- Link de volta para o feed --}}
    <a href="{{ route('postagens.index') }}"
       class="inline-flex items-center gap-1.5 text-zinc-400 hover:text-white
              text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar para o feed
    </a>

    {{-- Card da postagem (modo completo) --}}
    <x-postagem-card
        :postagem="$postagem"
        :curtidaPeloUsuario="$curtidaPeloUsuario"
        :compacto="false"
    />

    {{-- ═══════════════════════════════════════════════════════════════
         Seção de comentários
    ════════════════════════════════════════════════════════════════ --}}
    <section class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-5 sm:p-6">

        <h2 class="text-base font-semibold text-white mb-5">
            Comentários
            @if ($postagem->comentarios_count > 0)
                <span class="text-zinc-500 font-normal ml-1">({{ $postagem->comentarios_count }})</span>
            @endif
        </h2>

        {{-- Form de novo comentário --}}
        @auth
            <form action="{{ route('postagens.comentarios.store', $postagem) }}" method="post"
                  novalidate class="mb-6">
                @csrf
                <textarea
                    name="texto"
                    rows="3"
                    maxlength="1000"
                    minlength="3"
                    required
                    placeholder="Adicione um comentário..."
                    @class([
                        'w-full bg-[#0d0d0d] border rounded-lg px-3.5 py-2.5 resize-none',
                        'text-sm text-white placeholder-zinc-500 outline-none transition-colors',
                        'focus:ring-1',
                        'border-zinc-700 focus:border-blue-500 focus:ring-blue-500/40' => !$errors->has('texto'),
                        'border-red-500/70 focus:border-red-500 focus:ring-red-500/40' => $errors->has('texto'),
                    ])
                >{{ old('texto') }}</textarea>
                @error('texto')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
                <div class="flex justify-end mt-2">
                    <x-primary-button type="submit">Comentar</x-primary-button>
                </div>
            </form>
        @else
            <p class="text-zinc-400 text-sm mb-6">
                <a href="{{ route('usuario.login') }}"
                   class="text-blue-400 hover:text-blue-300 transition-colors font-medium">
                    Entre
                </a> para comentar.
            </p>
        @endauth

        {{-- Lista de comentários --}}
        @if ($postagem->comentariosRaiz->isEmpty())
            <p class="text-zinc-500 text-sm text-center py-8">
                Ainda não há comentários. Seja o primeiro!
            </p>
        @else
            <div class="space-y-5">
                @foreach ($postagem->comentariosRaiz->sortByDesc('criado_em') as $comentario)
                    <x-comentario-postagem :comentario="$comentario" />
                @endforeach
            </div>
        @endif

    </section>
</div>

@endsection
