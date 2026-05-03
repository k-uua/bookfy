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

            {{-- Nota --}}
            <div class="d-flex align-items-center my-3">
                @if ($nota !== null)
                    @for ($i = 1; $i <= 5; $i++)
                        @if ($i <= floor($nota))
                            <span class="text-warning fs-5">&#9733;</span>
                        @elseif ($i - $nota < 1)
                            <span class="text-warning fs-5">&#9734;</span>
                        @else
                            <span class="text-secondary fs-5">&#9734;</span>
                        @endif
                    @endfor
                    <span class="ms-2 text-muted">{{ number_format($nota, 1) }} / 5</span>
                    @if ($totalNotas)
                        <span class="ms-1 text-muted">({{ $totalNotas }} avaliações)</span>
                    @endif
                @else
                    <span class="text-muted">Sem avaliações ainda.</span>
                @endif
            </div>

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

@endsection
