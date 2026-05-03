@extends('layouts.app')
@section('titulo', 'Início')
@section('conteudo')

<div class="container mt-5">
    <h2 class="mb-4">Explorar por categoria</h2>
    <div class="row g-3">
        @foreach ([
            ['label' => 'Ficção',           'valor' => 'Fiction'],
            ['label' => 'Romance',           'valor' => 'Romance'],
            ['label' => 'Fantasia',          'valor' => 'Fantasy'],
            ['label' => 'Ficção Científica', 'valor' => 'Science Fiction'],
            ['label' => 'Mistério',          'valor' => 'Mystery'],
            ['label' => 'Terror',            'valor' => 'Horror'],
            ['label' => 'Aventura',          'valor' => 'Adventure'],
            ['label' => 'Drama',             'valor' => 'Drama'],
        ] as $categoria)
        <div class="col-6 col-md-3">
            <form action="{{ route('livros.categorias') }}" method="get">
                <input type="hidden" name="categoria" value="{{ $categoria['valor'] }}">
                <button type="submit" class="btn btn-outline-secondary w-100 py-3">
                    {{ $categoria['label'] }}
                </button>
            </form>
        </div>
        @endforeach
    </div>
</div>

@endsection