@extends('layouts.app')
@section('titulo', $livro['volumeInfo']['title'] ?? 'Detalhes do Livro')
@section('conteudo')

@php
    $info       = $livro['volumeInfo'] ?? [];
    $titulo     = $info['title'] ?? 'Sem título';
    $autores    = $info['authors'] ?? [];
    $sinopse    = strip_tags($info['description'] ?? 'Sem sinopse disponível.');
    $categorias = $info['categories'] ?? [];
    $nota       = $info['averageRating'] ?? null;
    $totalNotas = $info['ratingsCount'] ?? 0;
    $capa       = $info['imageLinks']['thumbnail'] ?? $info['imageLinks']['smallThumbnail'] ?? null;
    $paginas    = $info['pageCount'] ?? null;
    $editora    = $info['publisher'] ?? null;
    $publicado  = $info['publishedDate'] ?? null;
@endphp

<div class="container my-5">
    <div class="row g-5">

        {{-- Capa --}}
        <div class="col-md-3 text-center">
            @if ($capa)
                <img src="{{ $capa }}" alt="Capa de {{ $titulo }}"
                     class="img-fluid rounded shadow" style="max-height: 380px;">
            @else
                <div class="bg-secondary text-white d-flex align-items-center justify-content-center rounded shadow"
                     style="height: 280px;">
                    <span>Sem capa</span>
                </div>
            @endif
        </div>

        {{-- Informações --}}
        <div class="col-md-9">
            <h1 class="h2 fw-bold">{{ $titulo }}</h1>

            {{-- Autores --}}
            @if ($autores)
                <p class="text-muted mb-1">
                    <strong>{{ count($autores) > 1 ? 'Autores' : 'Autor' }}:</strong>
                    {{ implode(', ', $autores) }}
                </p>
            @endif

            {{-- Gêneros --}}
            @if ($categorias)
                <p class="mb-1">
                    <strong>Gênero:</strong>
                    @foreach ($categorias as $genero)
                        <span class="badge bg-secondary me-1">{{ $genero }}</span>
                    @endforeach
                </p>
            @endif

            {{-- Nota média do Google Books --}}
            <div class="d-flex align-items-center gap-2 mb-1">
                @if ($nota !== null)
                    <div class="d-flex">
                        @for ($i = 1; $i <= 5; $i++)
                            @if ($i <= floor($nota))
                                <span class="text-warning fs-5">&#9733;</span>
                            @elseif ($i - $nota < 1)
                                <span class="text-warning fs-5" style="opacity:.6;">&#9733;</span>
                            @else
                                <span class="text-secondary fs-5">&#9734;</span>
                            @endif
                        @endfor
                    </div>
                    <span class="text-muted small">{{ number_format($nota, 1) }} / 5</span>
                    @if ($totalNotas)
                        <span class="text-muted small">({{ number_format($totalNotas) }} avaliações)</span>
                    @endif
                @else
                    <span class="text-muted small">Sem avaliações ainda.</span>
                @endif
            </div>

            {{-- Widget de avaliação do usuário --}}
            @auth
                <div class="mb-3">
                    @if ($notaUsuario)
                        <p class="text-muted small mb-1">
                            Sua avaliação:
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="{{ $i <= $notaUsuario->nota ? 'text-warning' : 'text-secondary' }}">&#9733;</span>
                            @endfor
                            <span class="ms-1 fw-semibold">{{ (int) $notaUsuario->nota }} / 5</span>
                        </p>
                    @else
                        <p class="text-muted small mb-1">Você ainda não avaliou este livro.</p>
                    @endif

                    <form action="{{ route('livros.avaliar') }}" method="post" class="d-flex align-items-center gap-3">
                        @csrf
                        <input type="hidden" name="google_books_id"   value="{{ $livro['id'] }}">
                        <input type="hidden" name="titulo"            value="{{ $titulo }}">
                        <input type="hidden" name="capa_livro_url"    value="{{ $capa }}">
                        <input type="hidden" name="descricao"         value="{{ Str::limit($sinopse, 1000) }}">
                        <input type="hidden" name="paginas"           value="{{ $paginas }}">
                        <input type="hidden" name="data_lancamento"   value="{{ $publicado }}">
                        <input type="hidden" name="nota_google_books" value="{{ $nota }}">

                        <div class="star-widget d-flex flex-row-reverse">
                            @for ($i = 5; $i >= 1; $i--)
                                <input type="radio"
                                       id="estrela{{ $i }}"
                                       name="nota"
                                       value="{{ $i }}"
                                       class="d-none star-input"
                                       {{ ($notaUsuario && (int) $notaUsuario->nota === $i) ? 'checked' : '' }}>
                                <label for="estrela{{ $i }}"
                                       class="star-label fs-3 text-secondary"
                                       title="{{ $i }} {{ $i === 1 ? 'estrela' : 'estrelas' }}">&#9733;</label>
                            @endfor
                        </div>

                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            {{ $notaUsuario ? 'Atualizar' : 'Avaliar' }}
                        </button>
                    </form>

                    @error('nota')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <style>
                    .star-widget { gap: 2px; }
                    .star-label  { cursor: pointer; transition: color .1s; line-height: 1; }

                    /* Estrela hover e selecionadas à esquerda (por causa do flex-row-reverse) */
                    .star-label:hover,
                    .star-label:hover ~ .star-label,
                    .star-input:checked ~ .star-label {
                        color: #ffc107 !important;
                    }
                </style>
            @else
                <p class="text-muted small mb-3">
                    <a href="{{ route('usuario.login') }}">Entre</a> para avaliar este livro.
                </p>
            @endauth

            {{-- Detalhes secundários --}}
            <ul class="list-unstyled text-muted small mb-4">
                @if ($paginas)
                    <li><strong>Páginas:</strong> {{ $paginas }}</li>
                @endif
                @if ($editora)
                    <li><strong>Editora:</strong> {{ $editora }}</li>
                @endif
                @if ($publicado)
                    <li><strong>Publicado em:</strong> {{ $publicado }}</li>
                @endif
            </ul>

            {{-- Sinopse --}}
            <h5 class="fw-semibold">Sinopse</h5>
            <p class="text-body lh-base">{{ $sinopse }}</p>

            {{-- Adicionar à estante --}}
            <div class="mt-4 d-flex gap-2 align-items-center flex-wrap">
                @auth
                    {{-- Formulário único — submetido via JS --}}
                    <form id="formEstante" action="{{ route('estante.adicionar') }}" method="post">
                        @csrf
                        <input type="hidden" name="google_books_id"   value="{{ $livro['id'] }}">
                        <input type="hidden" name="titulo"            value="{{ $titulo }}">
                        <input type="hidden" name="capa_livro_url"    value="{{ $capa }}">
                        <input type="hidden" name="descricao"         value="{{ Str::limit($sinopse, 1000) }}">
                        <input type="hidden" name="paginas"           value="{{ $paginas }}">
                        <input type="hidden" name="data_lancamento"   value="{{ $publicado }}">
                        <input type="hidden" name="nota_google_books" value="{{ $nota }}">
                        <input type="hidden" name="nome_estante"      id="nomeEstanteHidden">
                    </form>

                    {{-- Dropdown principal --}}
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle"
                                type="button"
                                data-bs-toggle="dropdown"
                                aria-expanded="false">
                            Adicionar à estante
                        </button>

                        <ul class="dropdown-menu" id="dropdownEstante">

                            {{-- Fly-out: estantes existentes --}}
                            @if ($estantes->isNotEmpty())
                                <li class="position-relative" id="itemSubmenu">
                                    <button class="dropdown-item d-flex justify-content-between align-items-center"
                                            id="btnSubmenu" type="button">
                                        Selecionar estante
                                        <span class="ms-3">&#x203A;</span>
                                    </button>

                                    <ul class="dropdown-menu" id="submenuEstantes">
                                        @foreach ($estantes as $estante)
                                            <li>
                                                <button class="dropdown-item opcao-estante"
                                                        type="button"
                                                        data-nome="{{ $estante->nome }}">
                                                    {{ $estante->nome }}
                                                </button>
                                            </li>
                                        @endforeach
                                    </ul>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                            @endif

                            {{-- Criar nova estante --}}
                            <li>
                                <button class="dropdown-item"
                                        type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalCriarEstante">
                                    + Criar nova estante
                                </button>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('usuario.login') }}" class="btn btn-outline-primary">
                        Entre para adicionar à estante
                    </a>
                @endauth

                <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                    &larr; Voltar
                </a>
            </div>

            {{-- Modal: criar nova estante --}}
            @auth
                <div class="modal fade" id="modalCriarEstante" tabindex="-1" aria-labelledby="modalCriarEstanteLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-semibold" id="modalCriarEstanteLabel">Criar nova estante</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                            </div>
                            <div class="modal-body">
                                <label for="inputNomeEstante" class="form-label">Nome da estante</label>
                                <input
                                    type="text"
                                    id="inputNomeEstante"
                                    class="form-control"
                                    placeholder="Ex: Favoritos, Lendo, Para ler..."
                                    maxlength="255"
                                    autofocus
                                >
                                <div id="erroNomeEstante" class="text-danger small mt-1 d-none">
                                    Informe um nome para a estante.
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary" id="btnConfirmarCriarEstante">
                                    Criar e adicionar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    #submenuEstantes {
                        position: absolute;
                        top: 0;
                        left: 100%;
                        min-width: 200px;
                        display: none;
                    }
                    #itemSubmenu:hover #submenuEstantes,
                    #submenuEstantes:hover {
                        display: block;
                    }
                    @media (max-width: 576px) {
                        #submenuEstantes {
                            left: 0;
                            top: 100%;
                        }
                    }
                </style>

                <script>
                    (function () {
                        const form          = document.getElementById('formEstante');
                        const nomeHidden    = document.getElementById('nomeEstanteHidden');

                        // Selecionar estante existente via fly-out
                        document.querySelectorAll('.opcao-estante').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                nomeHidden.value = this.dataset.nome;
                                form.submit();
                            });
                        });

                        // Fly-out via clique (mobile-friendly)
                        const btnSubmenu = document.getElementById('btnSubmenu');
                        const submenu    = document.getElementById('submenuEstantes');
                        if (btnSubmenu && submenu) {
                            btnSubmenu.addEventListener('click', function (e) {
                                e.stopPropagation();
                                const visivel = submenu.style.display === 'block';
                                submenu.style.display = visivel ? 'none' : 'block';
                            });

                            // Fechar submenu ao fechar o dropdown principal
                            document.getElementById('dropdownEstante')
                                .addEventListener('hide.bs.dropdown', function () {
                                    submenu.style.display = 'none';
                                }, true);
                        }

                        // Criar nova estante via modal
                        const btnConfirmar  = document.getElementById('btnConfirmarCriarEstante');
                        const inputNome     = document.getElementById('inputNomeEstante');
                        const erroNome      = document.getElementById('erroNomeEstante');

                        btnConfirmar.addEventListener('click', function () {
                            const nome = inputNome.value.trim();
                            if (!nome) {
                                erroNome.classList.remove('d-none');
                                inputNome.focus();
                                return;
                            }
                            erroNome.classList.add('d-none');
                            nomeHidden.value = nome;
                            form.submit();
                        });

                        // Limpar estado do modal ao abrir
                        document.getElementById('modalCriarEstante').addEventListener('show.bs.modal', function () {
                            inputNome.value = '';
                            erroNome.classList.add('d-none');
                        });

                        // Submeter com Enter no input do modal
                        inputNome.addEventListener('keydown', function (e) {
                            if (e.key === 'Enter') {
                                e.preventDefault();
                                btnConfirmar.click();
                            }
                        });
                    })();
                </script>
            @endauth
        </div>

    </div>
</div>

{{-- ══════════════════════════════════════════════════
     Seção de comentários
═══════════════════════════════════════════════════ --}}
<div class="container my-5">
    <h4 class="fw-bold mb-4">
        Comentários
        @if ($comentarios->isNotEmpty())
            <span class="fs-6 text-muted fw-normal">({{ $comentarios->count() }})</span>
        @endif
    </h4>

    {{-- Formulário: apenas para autenticados --}}
    @auth
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form action="{{ route('livros.comentar') }}" method="post" novalidate>
                    @csrf

                    {{-- Dados do livro para firstOrCreate --}}
                    <input type="hidden" name="google_books_id"   value="{{ $livro['id'] }}">
                    <input type="hidden" name="titulo"            value="{{ $titulo }}">
                    <input type="hidden" name="capa_livro_url"    value="{{ $capa }}">
                    <input type="hidden" name="descricao"         value="{{ Str::limit($sinopse, 1000) }}">
                    <input type="hidden" name="paginas"           value="{{ $paginas }}">
                    <input type="hidden" name="data_lancamento"   value="{{ $publicado }}">
                    <input type="hidden" name="nota_google_books" value="{{ $nota }}">

                    {{-- Futura integração: nota selecionada pelo usuário --}}
                    {{-- <input type="hidden" name="nota_usuario" value=""> --}}

                    <div class="mb-3">
                        <label for="texto" class="form-label fw-semibold">Seu comentário</label>
                        <textarea
                            id="texto"
                            name="texto"
                            rows="3"
                            maxlength="1000"
                            class="form-control @error('texto') is-invalid @enderror"
                            placeholder="Compartilhe sua opinião sobre o livro..."
                        >{{ old('texto') }}</textarea>

                        @error('texto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        <div class="text-end mt-1">
                            <small class="text-muted" id="contadorTexto">0 / 1000</small>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Publicar comentário</button>
                </form>
            </div>
        </div>
    @else
        <p class="text-muted mb-4">
            <a href="{{ route('usuario.login') }}">Entre</a> para deixar um comentário.
        </p>
    @endauth

    {{-- Lista de comentários --}}
    @if ($comentarios->isEmpty())
        <p class="text-muted">Ainda não há comentários. Seja o primeiro!</p>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach ($comentarios as $comentario)
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">

                        {{-- Cabeçalho: avatar + nome + ações --}}
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                     style="width:36px; height:36px; font-size:.9rem;">
                                    {{ mb_strtoupper(mb_substr($comentario->usuario->nome ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="fw-semibold">{{ $comentario->usuario->nome ?? 'Usuário removido' }}</span>
                                    <span class="text-muted small ms-2">
                                        {{ $comentario->criado_em->diffForHumans() }}
                                    </span>

                                    {{-- Nota atribuída pelo comentador a este livro --}}
                                    @php $notaComentador = $notasPorUsuario[$comentario->id_usuario] ?? null; @endphp
                                    @if ($notaComentador)
                                        <span class="ms-2 small" title="{{ (int) $notaComentador->nota }} estrelas">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="{{ $i <= $notaComentador->nota ? 'text-warning' : 'text-secondary' }}" style="font-size:.85rem;">&#9733;</span>
                                            @endfor
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Botão deletar (somente o autor) --}}
                            @auth
                                @if (Auth::id() === $comentario->id_usuario)
                                    <form action="{{ route('livros.comentarios.deletar', $comentario) }}"
                                          method="post" class="flex-shrink-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-link text-danger p-0"
                                                title="Remover comentário"
                                                onclick="return confirm('Remover este comentário?')">
                                            <small>Remover</small>
                                        </button>
                                    </form>
                                @endif
                            @endauth
                        </div>

                        {{-- Texto do comentário --}}
                        <p class="mb-2 lh-base" style="white-space: pre-line;">{{ $comentario->texto }}</p>

                        {{-- Botão responder --}}
                        @auth
                            <button type="button"
                                    class="btn btn-link p-0 text-muted small btn-responder"
                                    data-target="responder-{{ $comentario->id }}">
                                ↩ Responder
                            </button>

                            {{-- Formulário de resposta (oculto por padrão) --}}
                            <div id="responder-{{ $comentario->id }}" class="mt-3 d-none">
                                <form action="{{ route('livros.comentarios.responder', $comentario) }}" method="post" novalidate>
                                    @csrf
                                    <div class="mb-2">
                                        <textarea name="texto"
                                                  rows="2"
                                                  maxlength="1000"
                                                  class="form-control form-control-sm"
                                                  placeholder="Escreva sua resposta..."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">Publicar resposta</button>
                                        <button type="button"
                                                class="btn btn-outline-secondary btn-sm btn-cancelar-resposta"
                                                data-target="responder-{{ $comentario->id }}">
                                            Cancelar
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endauth

                        {{-- Respostas --}}
                        @if ($comentario->respostas->isNotEmpty())
                            <div class="mt-3 ps-4 border-start border-2 border-primary-subtle d-flex flex-column gap-2">
                                @foreach ($comentario->respostas->sortBy('criado_em') as $resposta)
                                    <div class="bg-light rounded p-3">
                                        <div class="d-flex justify-content-between align-items-start gap-2">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
                                                     style="width:28px; height:28px; font-size:.75rem;">
                                                    {{ mb_strtoupper(mb_substr($resposta->usuario->nome ?? '?', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <span class="fw-semibold small">{{ $resposta->usuario->nome ?? 'Usuário removido' }}</span>
                                                    <span class="text-muted" style="font-size:.75rem;">
                                                        &nbsp;{{ $resposta->criado_em->diffForHumans() }}
                                                    </span>
                                                </div>
                                            </div>

                                            {{-- Deletar resposta --}}
                                            @auth
                                                @if (Auth::id() === $resposta->id_usuario)
                                                    <form action="{{ route('livros.comentarios.deletar', $resposta) }}"
                                                          method="post">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="btn btn-link text-danger p-0"
                                                                style="font-size:.75rem;"
                                                                onclick="return confirm('Remover esta resposta?')">
                                                            Remover
                                                        </button>
                                                    </form>
                                                @endif
                                            @endauth
                                        </div>
                                        <p class="mb-0 small lh-base" style="white-space: pre-line;">{{ $resposta->texto }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
    (function () {
        // Contador de caracteres no formulário principal
        const textarea = document.getElementById('texto');
        const contador = document.getElementById('contadorTexto');
        if (textarea && contador) {
            const atualizar = () => { contador.textContent = textarea.value.length + ' / 1000'; };
            textarea.addEventListener('input', atualizar);
            atualizar();
        }

        // Botões "Responder" — exibe/oculta formulário inline
        document.querySelectorAll('.btn-responder').forEach(btn => {
            btn.addEventListener('click', () => {
                const alvo = document.getElementById(btn.dataset.target);
                if (!alvo) return;
                alvo.classList.toggle('d-none');
                if (!alvo.classList.contains('d-none')) {
                    alvo.querySelector('textarea')?.focus();
                }
            });
        });

        // Botões "Cancelar" dentro dos formulários de resposta
        document.querySelectorAll('.btn-cancelar-resposta').forEach(btn => {
            btn.addEventListener('click', () => {
                const alvo = document.getElementById(btn.dataset.target);
                if (!alvo) return;
                alvo.classList.add('d-none');
                const ta = alvo.querySelector('textarea');
                if (ta) ta.value = '';
            });
        });
    })();
</script>

@endsection
