@extends('layouts.app')
@section('titulo', isset($titulo) ? $titulo : 'Resultados')
@section('conteudo')

<div class="container mt-4">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        @foreach ($livros as $livro)
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="text-center p-3 bg-light">
                    <img src="{{ str_replace('http://', 'https://', $livro['volumeInfo']['imageLinks']['thumbnail'] ?? asset('images/default-book-cover.png')) }}"
                         class="card-img-top img-fluid"
                         style="max-height: 200px; width: auto;"
                         alt="Capa do livro {{ $livro['volumeInfo']['title'] ?? '' }}">
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $livro['volumeInfo']['title'] ?? 'Sem título' }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">
                        {{ $livro['volumeInfo']['authors'][0] ?? 'Autor desconhecido' }}
                    </h6>
                    <p class="card-text text-muted small" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden;">
                        {{ strip_tags($livro['volumeInfo']['description'] ?? 'Sem descrição disponível') }}
                    </p>
                </div>
                <div class="card-footer">
                    <a href="{{ route('livros.show', $livro['id']) }}" class="btn btn-outline-primary btn-sm w-100">
                        Ver detalhes
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if ($livros->isEmpty())
        <div class="text-center py-5">
            <p class="text-muted fs-5">Nenhum livro encontrado.</p>
            <a href="{{ route('livros.index') }}" class="btn btn-primary mt-2">Voltar ao início</a>
        </div>
    @endif

    <div class="d-flex justify-content-center mt-4">
        {{ $livros->links('pagination::bootstrap-5') }}
    </div>
</div>

@endsection
