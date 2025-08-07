@extends('layouts.app')
@section('titulo', 'Início')
@section('conteudo')

<body> 
    <form action="{{ route('livros.categorias') }}" method="get">
        <input type="hidden" name="categoria" value="Romance">
        <button type="submit">Romance</button>
    </form>

    <form action="{{ route('livros.categorias') }}" method="get">
        <input type="hidden" name="categoria" value="Fiction">
        <button type="submit">Ficção</button>
    </form>

    <form action="{{ route('livros.categorias') }}" method="get">
        <input type="hidden" name="categoria" value="Anime">
        <button type="submit">Manga</button>
    </form>

    <form action="{{ route('livros.categorias') }}" method="get">
        <input type="hidden" name="categoria" value="History">
        <button type="submit">História e mundo</button>
    </form>
</body>

@endsection
