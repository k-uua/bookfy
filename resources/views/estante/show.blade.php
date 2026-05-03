@extends('layouts.app')
@section('titulo', $estante->nome)
@section('conteudo')

@php
    $statusLabel = [
        'quero_ler'  => ['label' => 'Quero ler',  'class' => 'bg-secondary'],
        'lendo'      => ['label' => 'Lendo',       'class' => 'bg-primary'],
        'lido'       => ['label' => 'Lido',        'class' => 'bg-success'],
    ];
@endphp

<div class="container my-5">

    {{-- Cabeçalho da estante --}}
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('estante.index') }}" class="btn btn-outline-secondary btn-sm">
            &larr; Voltar
        </a>
        <div>
            <h2 class="fw-bold mb-0">{{ $estante->nome }}</h2>
            <span class="text-muted small">
                {{ $livros->count() }} {{ Str::plural('livro', $livros->count()) }}
            </span>
        </div>
    </div>

    @if ($livros->isEmpty())
        <div class="text-center py-5">
            <p class="text-muted fs-5">Esta estante está vazia.</p>
            <a href="{{ route('livros.index') }}" class="btn btn-primary mt-2">Adicionar livros</a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
            @foreach ($livros as $livro)
                @php
                    $status = $livro->pivot->status ?? 'quero_ler';
                    $badge  = $statusLabel[$status] ?? $statusLabel['quero_ler'];
                @endphp
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">

                        {{-- Capa --}}
                        <a href="{{ route('livros.show', $livro->google_books_id) }}"
                           class="text-decoration-none">
                            <div class="text-center bg-light pt-3 px-3"
                                 style="min-height: 200px; display:flex; align-items:center; justify-content:center;">
                                @if ($livro->capa_livro_url)
                                    <img src="{{ $livro->capa_livro_url }}"
                                         alt="Capa de {{ $livro->titulo }}"
                                         class="img-fluid rounded"
                                         style="max-height: 190px; object-fit: contain;">
                                @else
                                    <div class="text-muted fs-1">📖</div>
                                @endif
                            </div>
                        </a>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title fw-semibold mb-1" style="line-height:1.3;">
                                <a href="{{ route('livros.show', $livro->google_books_id) }}"
                                   class="text-dark text-decoration-none stretched-link">
                                    {{ $livro->titulo }}
                                </a>
                            </h6>

                            <div class="mt-auto pt-2 d-flex align-items-center justify-content-between">
                                <span class="badge {{ $badge['class'] }} rounded-pill">
                                    {{ $badge['label'] }}
                                </span>
                                @if ($livro->pivot->favorito)
                                    <span title="Favorito">⭐</span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>

@endsection
