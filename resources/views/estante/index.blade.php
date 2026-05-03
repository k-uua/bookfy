@extends('layouts.app')
@section('titulo', 'Minhas Estantes')
@section('conteudo')

<div class="container my-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Minhas Estantes</h2>
        <span class="text-muted">{{ $estantes->count() }} {{ Str::plural('estante', $estantes->count()) }}</span>
    </div>

    @if ($estantes->isEmpty())
        <div class="text-center py-5">
            <p class="text-muted fs-5">Você ainda não tem nenhuma estante.</p>
            <p class="text-muted">Adicione um livro a uma estante para começar.</p>
            <a href="{{ route('livros.index') }}" class="btn btn-primary mt-2">Explorar livros</a>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
            @foreach ($estantes as $estante)
                <div class="col">
                    <a href="{{ route('estante.show', $estante) }}"
                       class="text-decoration-none text-dark">
                        <div class="card h-100 shadow-sm border-0 estante-card">
                            {{-- Cabeçalho colorido --}}
                            <div class="card-header border-0 py-4"
                                 style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                                <div class="text-white text-center fs-1">📚</div>
                            </div>

                            <div class="card-body">
                                <h5 class="card-title fw-semibold mb-1 text-truncate">
                                    {{ $estante->nome }}
                                </h5>
                                <p class="text-muted small mb-0">
                                    {{ $estante->livros_count }}
                                    {{ Str::plural('livro', $estante->livros_count) }}
                                </p>
                            </div>

                            <div class="card-footer bg-white border-0 pt-0 pb-3">
                                <span class="btn btn-outline-primary btn-sm w-100">Ver estante</span>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    @endif

</div>

<style>
    .estante-card {
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        cursor: pointer;
    }
    .estante-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    }
</style>

@endsection
