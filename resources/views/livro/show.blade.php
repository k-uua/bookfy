@extends('layouts.main')
@section('titulo', $livro['volumeInfo']['title'] ?? 'Detalhes do Livro')

@php
    $info             = $livro['volumeInfo'] ?? [];
    $titulo           = $info['title'] ?? 'Sem título';
    $autores          = $info['authors'] ?? [];
    $sinopse          = strip_tags($info['description'] ?? 'Sem sinopse disponível.');
    $categorias       = $info['categories'] ?? [];
    $notaGoogle       = $info['averageRating'] ?? null;
    $totalNotasGoogle = $info['ratingsCount'] ?? 0;
    $capa             = $info['imageLinks']['thumbnail'] ?? $info['imageLinks']['smallThumbnail'] ?? null;
    $paginas          = $info['pageCount'] ?? null;
    $editora          = $info['publisher'] ?? null;
    $publicado        = $info['publishedDate'] ?? null;

    // Hidden fields reutilizados em vários forms
    $hiddenLivro = [
        'google_books_id'   => $livro['id'] ?? '',
        'titulo'            => $titulo,
        'capa_livro_url'    => $capa,
        'descricao'         => Str::limit($sinopse, 1000),
        'paginas'           => $paginas,
        'data_lancamento'   => $publicado,
        'nota_google_books' => $notaGoogle,
    ];
@endphp

@section('conteudo')

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <h1 class="sr-only">{{ $titulo }}</h1>

    {{-- ═══════════════════════════════════════════════════════════════
         Layout principal · 2 colunas
         Esquerda  (~280px): capa + card avaliação + card estante
         Direita   (1fr)   : sinópse + ratings (com sub-seções)
    ════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row gap-5">

        {{-- ───────────────────────────────────────────
             Coluna esquerda
        ──────────────────────────────────────────── --}}
        <aside class="w-full md:w-72 md:shrink-0 space-y-5">

            {{-- Capa --}}
            <div class="rounded-2xl overflow-hidden bg-zinc-900 shadow-xl shadow-black/50 ring-1 ring-zinc-800/60">
                @if ($capa)
                    <img src="{{ $capa }}"
                         alt="Capa de {{ $titulo }}"
                         class="w-full h-auto block">
                @else
                    <div class="aspect-[2/3] flex items-center justify-center text-zinc-700 text-5xl">
                        📖
                    </div>
                @endif
            </div>

            @auth
                {{-- Card · Deixe sua avaliação! --}}
                <div class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-5">
                    <form id="formAvaliar" action="{{ route('livros.avaliar') }}" method="post">
                        @csrf
                        @foreach ($hiddenLivro as $campo => $valor)
                            <input type="hidden" name="{{ $campo }}" value="{{ $valor }}">
                        @endforeach

                        <p class="text-sm text-zinc-300 mb-2">Deixe sua avaliação!</p>

                        <div class="flex flex-row-reverse justify-end gap-1 star-widget">
                            @for ($i = 5; $i >= 1; $i--)
                                <input type="radio"
                                       id="estrela{{ $i }}"
                                       name="nota"
                                       value="{{ $i }}"
                                       class="hidden star-input"
                                       {{ ($notaUsuario && (int) $notaUsuario->nota === $i) ? 'checked' : '' }}>
                                <label for="estrela{{ $i }}"
                                       class="star-label cursor-pointer transition-colors"
                                       title="{{ $i }} {{ $i === 1 ? 'estrela' : 'estrelas' }}">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </label>
                            @endfor
                        </div>

                        @error('nota')
                            <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </form>
                </div>

                {{-- Card · Adicionar à minha estante (card-button inteiro) --}}
                <button type="button" data-modal-open="modalEstante"
                        class="w-full flex items-center gap-3 bg-[#161616] hover:bg-[#1a1a1a]
                               border border-zinc-800/60 hover:border-zinc-700 rounded-2xl
                               px-5 py-4 text-sm text-zinc-300 hover:text-white text-left transition-colors">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    Adicionar à minha estante
                </button>
            @else
                <div class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-5">
                    <p class="text-sm text-zinc-400 mb-3">
                        Faça login para avaliar e organizar este livro.
                    </p>
                    <a href="{{ route('usuario.login') }}"
                       class="block w-full bg-blue-600 hover:bg-blue-500 text-white text-center
                              text-sm font-semibold py-2.5 rounded-lg transition-colors">
                        Entrar
                    </a>
                </div>
            @endauth
        </aside>

        {{-- ───────────────────────────────────────────
             Coluna direita: Sinópse + Ratings
        ──────────────────────────────────────────── --}}
        <div class="flex-1 min-w-0 space-y-5">

            {{-- Card · Sinópse --}}
            <div class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-6">
                <h2 class="text-base font-semibold text-white mb-3">Sinópse</h2>
                <p class="text-zinc-300 text-sm leading-relaxed whitespace-pre-line">
                    {{ $sinopse }}
                </p>
            </div>

            {{-- Card · Ratings + Categoria + Autor --}}
            <div class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-6 space-y-5">

                <h2 class="text-base font-semibold text-white flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 3.679a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69L9.049 2.927z"/>
                    </svg>
                    Ratings
                </h2>

                {{-- Boxes de notas: Bookfy + Google Books --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    {{-- Bookfy --}}
                    <div class="bg-[#0d0d0d] border border-zinc-800/60 rounded-xl p-4">
                        <p class="text-zinc-400 text-xs mb-2">Bookfy</p>
                        <div class="flex items-center gap-2">
                            <x-star-display :nota="$notaBookfy ?? 0" size="md" />
                            <span class="text-zinc-200 text-sm font-medium">
                                {{ $notaBookfy ? number_format($notaBookfy, 1) : '—' }}
                            </span>
                        </div>
                        @if ($totalAvaliacoesBookfy > 0)
                            <p class="text-zinc-500 text-[11px] mt-1">
                                {{ $totalAvaliacoesBookfy }} {{ Str::plural('avaliação', $totalAvaliacoesBookfy, ['avaliação', 'avaliações']) }}
                            </p>
                        @endif
                    </div>

                    {{-- Google Books --}}
                    <div class="bg-[#0d0d0d] border border-zinc-800/60 rounded-xl p-4">
                        <p class="text-zinc-400 text-xs mb-2">Google Books</p>
                        <div class="flex items-center gap-2">
                            <x-star-display :nota="$notaGoogle ?? 0" size="md" />
                            <span class="text-zinc-200 text-sm font-medium">
                                {{ $notaGoogle ? number_format($notaGoogle, 1) : '—' }}
                            </span>
                        </div>
                        @if ($totalNotasGoogle > 0)
                            <p class="text-zinc-500 text-[11px] mt-1">
                                {{ number_format($totalNotasGoogle) }} avaliações
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Categoria --}}
                @if (!empty($categorias))
                    <div>
                        <p class="text-zinc-400 text-xs mb-2 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            Categoria
                        </p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($categorias as $cat)
                                <span class="px-3 py-1.5 rounded-lg bg-[#0d0d0d] border border-zinc-700
                                             text-sm text-zinc-300">
                                    {{ $cat }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Autor --}}
                @if (!empty($autores))
                    <div>
                        <p class="text-zinc-400 text-xs mb-2">{{ count($autores) > 1 ? 'Autores' : 'Autor' }}</p>
                        <div class="bg-[#0d0d0d] border border-zinc-800/60 rounded-xl p-4 space-y-2">
                            @foreach ($autores as $autor)
                                <p class="text-white text-sm font-semibold">{{ $autor }}</p>
                            @endforeach
                            <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-zinc-500 pt-1">
                                @if ($publicado)
                                    <span>Publicado em {{ $publicado }}</span>
                                @endif
                                @if ($editora)
                                    <span>Editora: {{ $editora }}</span>
                                @endif
                                @if ($paginas)
                                    <span>{{ $paginas }} páginas</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         Card · Avaliações (full width)
    ════════════════════════════════════════════════════════════════ --}}
    <div class="mt-8 bg-[#161616] border border-zinc-800/60 rounded-2xl p-6 sm:p-8">

        {{-- Header --}}
        <div class="flex items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-semibold text-white">
                Avaliações
                @if ($comentarios->isNotEmpty())
                    <span class="text-zinc-500 text-base font-normal ml-1">({{ $comentarios->count() }})</span>
                @endif
            </h2>

            @auth
                <button type="button" data-toggle="formNovoComentario"
                        class="flex items-center gap-2 bg-zinc-800 hover:bg-zinc-700 text-white
                               text-sm font-medium px-4 py-2 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    Adicione sua crítica
                </button>
            @endauth
        </div>

        {{-- Form de novo comentário --}}
        @auth
            <div id="formNovoComentario" class="hidden mb-6">
                <form action="{{ route('livros.comentar') }}" method="post" novalidate>
                    @csrf
                    @foreach ($hiddenLivro as $campo => $valor)
                        <input type="hidden" name="{{ $campo }}" value="{{ $valor }}">
                    @endforeach

                    <textarea name="texto" id="texto" rows="3" maxlength="1000"
                              placeholder="Compartilhe sua opinião sobre o livro..."
                              @class([
                                  'w-full bg-[#1c1c1e] border rounded-lg px-4 py-3 text-sm text-white',
                                  'placeholder-zinc-500 outline-none transition-colors resize-none',
                                  'border-zinc-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/40' => !$errors->has('texto'),
                                  'border-red-500/70 focus:border-red-500 focus:ring-1 focus:ring-red-500/40' => $errors->has('texto'),
                              ])>{{ old('texto') }}</textarea>

                    @error('texto')
                        <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror

                    <div class="flex items-center justify-between mt-2 gap-3 flex-wrap">
                        <span class="text-zinc-500 text-xs" id="contadorTexto">0 / 1000</span>
                        <div class="flex gap-2">
                            <button type="button" data-toggle="formNovoComentario"
                                    class="text-sm text-zinc-400 hover:text-white transition-colors px-4 py-2">
                                Cancelar
                            </button>
                            <x-primary-button type="submit">Publicar</x-primary-button>
                        </div>
                    </div>
                </form>
            </div>
        @else
            <p class="text-zinc-400 text-sm mb-6">
                <a href="{{ route('usuario.login') }}"
                   class="text-blue-400 hover:text-blue-300 transition-colors font-medium">
                    Entre
                </a>
                para deixar uma avaliação.
            </p>
        @endauth

        {{-- Lista --}}
        @if ($comentarios->isEmpty())
            <p class="text-zinc-500 text-sm text-center py-8">
                Ainda não há avaliações. Seja o primeiro!
            </p>
        @else
            <div class="space-y-4">
                @foreach ($comentarios as $comentario)
                    @php $notaComentador = $notasPorUsuario[$comentario->id_usuario] ?? null; @endphp

                    <div class="bg-[#0d0d0d] border border-zinc-800/60 rounded-xl p-5">

                        {{-- Header: avatar + nome + data + nota --}}
                        <div class="flex items-start justify-between gap-4 mb-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center
                                            text-white font-bold text-xs shrink-0">
                                    {{ mb_strtoupper(mb_substr($comentario->usuario->nome ?? '?', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-white text-sm truncate">
                                        {{ $comentario->usuario->nome ?? 'Usuário removido' }}
                                    </p>
                                    <p class="text-zinc-500 text-xs">
                                        {{ $comentario->criado_em->diffForHumans() }}
                                    </p>
                                </div>
                            </div>

                            @if ($notaComentador)
                                <div class="flex items-center gap-1.5 shrink-0">
                                    <x-star-display :nota="$notaComentador->nota" size="sm" />
                                    <span class="text-zinc-200 text-sm font-medium">
                                        {{ (int) $notaComentador->nota }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Texto --}}
                        <p class="text-zinc-300 text-sm leading-relaxed whitespace-pre-line mb-3">
                            {{ $comentario->texto }}
                        </p>

                        {{-- Ações --}}
                        @auth
                            <div class="flex items-center gap-4">
                                <button type="button" data-toggle="responder-{{ $comentario->id }}"
                                        class="text-zinc-400 hover:text-white text-xs transition-colors">
                                    ↩ Responder
                                </button>

                                @if (Auth::id() === $comentario->id_usuario)
                                    <form action="{{ route('livros.comentarios.deletar', $comentario) }}"
                                          method="post" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-red-400 hover:text-red-300 text-xs transition-colors"
                                                onclick="return confirm('Remover esta avaliação?')">
                                            Remover
                                        </button>
                                    </form>
                                @endif
                            </div>

                            {{-- Form de resposta --}}
                            <div id="responder-{{ $comentario->id }}" class="hidden mt-3">
                                <form action="{{ route('livros.comentarios.responder', $comentario) }}" method="post">
                                    @csrf
                                    <textarea name="texto" rows="2" maxlength="1000"
                                              placeholder="Escreva sua resposta..."
                                              class="w-full bg-[#1c1c1e] border border-zinc-700 rounded-lg px-3 py-2
                                                     text-sm text-white placeholder-zinc-500 outline-none resize-none
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

                        {{-- Respostas (threading) --}}
                        @if ($comentario->respostas->isNotEmpty())
                            <div class="mt-4 ml-3 pl-4 border-l-2 border-zinc-800 space-y-3">
                                @foreach ($comentario->respostas->sortBy('criado_em') as $resposta)
                                    <div class="bg-[#161616] border border-zinc-800/60 rounded-lg p-3">
                                        <div class="flex items-start justify-between gap-2 mb-1">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <div class="w-6 h-6 rounded-full bg-zinc-700 flex items-center justify-center
                                                            text-white font-bold text-[10px] shrink-0">
                                                    {{ mb_strtoupper(mb_substr($resposta->usuario->nome ?? '?', 0, 1)) }}
                                                </div>
                                                <span class="text-white text-xs font-semibold truncate">
                                                    {{ $resposta->usuario->nome ?? 'Usuário' }}
                                                </span>
                                                <span class="text-zinc-500 text-[11px] shrink-0">
                                                    {{ $resposta->criado_em->diffForHumans() }}
                                                </span>
                                            </div>

                                            @auth
                                                @if (Auth::id() === $resposta->id_usuario)
                                                    <form action="{{ route('livros.comentarios.deletar', $resposta) }}"
                                                          method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="text-red-400 hover:text-red-300 text-[11px] transition-colors"
                                                                onclick="return confirm('Remover esta resposta?')">
                                                            Remover
                                                        </button>
                                                    </form>
                                                @endif
                                            @endauth
                                        </div>
                                        <p class="text-zinc-300 text-xs leading-relaxed whitespace-pre-line">
                                            {{ $resposta->texto }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════
     Modal · Adicionar à estante
════════════════════════════════════════════════════════════════ --}}
@auth
    <div id="modalEstante"
         class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-8"
         data-modal-backdrop>

        <div class="bg-[#161616] border border-zinc-800 rounded-2xl w-full max-w-md p-6 shadow-2xl">

            <h3 class="text-lg font-semibold text-white mb-1">Adicionar à estante</h3>
            <p class="text-zinc-400 text-sm mb-5">
                Selecione uma estante existente ou crie uma nova.
            </p>

            {{-- Form base — submetido pelos botões abaixo --}}
            <form id="formEstante" action="{{ route('estante.adicionar') }}" method="post">
                @csrf
                @foreach ($hiddenLivro as $campo => $valor)
                    <input type="hidden" name="{{ $campo }}" value="{{ $valor }}">
                @endforeach
                <input type="hidden" name="nome_estante" id="nomeEstanteHidden">
            </form>

            {{-- Estantes existentes --}}
            @if ($estantes->isNotEmpty())
                <div class="space-y-2 mb-5">
                    <p class="text-xs text-zinc-500 uppercase tracking-wider mb-2">Suas estantes</p>
                    @foreach ($estantes as $estante)
                        <button type="button"
                                class="opcao-estante w-full text-left bg-[#0d0d0d] hover:bg-zinc-900
                                       border border-zinc-800 hover:border-zinc-700 rounded-lg
                                       px-4 py-3 text-sm text-white transition-colors flex items-center gap-3"
                                data-nome="{{ $estante->nome }}">
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                            </svg>
                            {{ $estante->nome }}
                        </button>
                    @endforeach
                </div>
                <hr class="border-zinc-800 my-4">
            @endif

            {{-- Criar nova --}}
            <div>
                <p class="text-xs text-zinc-500 uppercase tracking-wider mb-2">Criar nova estante</p>
                <div class="flex gap-2">
                    <input type="text" id="inputNomeEstante"
                           placeholder="Ex: Favoritos, Lendo..."
                           maxlength="255"
                           class="flex-1 bg-[#0d0d0d] border border-zinc-700 rounded-lg px-3 py-2
                                  text-sm text-white placeholder-zinc-500 outline-none transition-colors
                                  focus:border-blue-500 focus:ring-1 focus:ring-blue-500/40">
                    <button type="button" id="btnConfirmarCriarEstante"
                            class="bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold
                                   px-4 rounded-lg transition-colors">
                        Criar
                    </button>
                </div>
                <p id="erroNomeEstante" class="text-red-400 text-xs mt-2 hidden">
                    Informe um nome para a estante.
                </p>
            </div>

            <div class="flex justify-end mt-6">
                <button type="button" data-modal-close="modalEstante"
                        class="text-sm text-zinc-400 hover:text-white transition-colors px-4 py-2">
                    Cancelar
                </button>
            </div>

        </div>
    </div>
@endauth

@endsection

@push('scripts')
<script>
    (function () {
        // ── Auto-submit do widget de estrelas ─────────────────────────
        const formAvaliar = document.getElementById('formAvaliar');
        if (formAvaliar) {
            formAvaliar.querySelectorAll('input.star-input').forEach((input) => {
                input.addEventListener('change', () => formAvaliar.submit());
            });
        }

        // ── Contador do textarea principal ────────────────────────────
        const textarea = document.getElementById('texto');
        const contador = document.getElementById('contadorTexto');
        if (textarea && contador) {
            const atualizar = () => contador.textContent = textarea.value.length + ' / 1000';
            textarea.addEventListener('input', atualizar);
            atualizar();
        }

        // ── Modal estante: ações específicas ──────────────────────────
        const formEstante = document.getElementById('formEstante');
        const nomeHidden  = document.getElementById('nomeEstanteHidden');

        if (formEstante && nomeHidden) {
            // Selecionar estante existente
            document.querySelectorAll('.opcao-estante').forEach((btn) => {
                btn.addEventListener('click', () => {
                    nomeHidden.value = btn.dataset.nome;
                    formEstante.submit();
                });
            });

            // Criar nova estante
            const btnConfirmar = document.getElementById('btnConfirmarCriarEstante');
            const inputNome    = document.getElementById('inputNomeEstante');
            const erroNome     = document.getElementById('erroNomeEstante');

            const submeterNova = () => {
                const nome = inputNome.value.trim();
                if (!nome) {
                    erroNome.classList.remove('hidden');
                    inputNome.focus();
                    return;
                }
                erroNome.classList.add('hidden');
                nomeHidden.value = nome;
                formEstante.submit();
            };

            btnConfirmar?.addEventListener('click', submeterNova);
            inputNome?.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submeterNova();
                }
            });
        }
    })();
</script>

<style>
    /* Star widget interativo (form de avaliação) — ordem reversa via flex-row-reverse */
    .star-widget .star-label svg { color: rgb(63 63 70); transition: color .15s; }
    .star-widget .star-label:hover svg,
    .star-widget .star-label:hover ~ .star-label svg,
    .star-widget .star-input:checked ~ .star-label svg {
        color: rgb(251 191 36);
    }
</style>
@endpush
