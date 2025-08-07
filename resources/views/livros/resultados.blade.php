@extends('layouts.app')
@section('titulo', 'Início')
@section('conteudo')
<body>
    
<div class="container mt-4">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4">
        @foreach ($livros as $livro)
        <div class="col">
            <div class="card h-100 shadow-sm">
                <div class="text-center p-3 bg-light">
                    <img src="{{ $livro['volumeInfo']['imageLinks']['thumbnail'] ?? asset('images/default-book-cover.png') }}" 
                         class="card-img-top img-fluid" 
                         style="max-height: 200px; width: auto;"
                         alt="Capa do livro {{ $livro['volumeInfo']['title'] ?? '' }}">
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $livro['volumeInfo']['title'] ?? 'Sem título' }}</h5>
                    <h6 class="card-subtitle mb-2 text-muted">
                        Autor: {{ $livro['volumeInfo']['authors'][0] ?? 'Desconhecido' }}
                    </h6>
                    <p class="card-text text-truncate" style="max-height: 72px; overflow: hidden;">
                        {{ strip_tags($livro['volumeInfo']['description'] ?? 'Sem descrição disponível') }}
                    </p>
                </div>
                <div class="card-footer bg-white">
                    <a href="#" class="btn btn-outline-primary btn-sm">Detalhes</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $livros->links('pagination::bootstrap-5') }}
    </div>
</div>

</body>
@endsection