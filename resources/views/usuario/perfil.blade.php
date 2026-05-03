@extends('layouts.app')
@section('titulo', 'Perfil — ' . $usuario->nome)
@section('conteudo')

<div class="container my-5">

    {{-- Cabeçalho do perfil --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row align-items-center g-4">

                {{-- Avatar com inicial do nome --}}
                <div class="col-auto">
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white fw-bold"
                         style="width:80px; height:80px; font-size:2rem;">
                        {{ mb_strtoupper(mb_substr($usuario->nome, 0, 1)) }}
                    </div>
                </div>

                {{-- Nome e e-mail --}}
                <div class="col">
                    <h2 class="mb-0 fw-bold">{{ $usuario->nome }}</h2>
                    <span class="text-muted">{{ $usuario->email }}</span>
                </div>

                {{-- Badge de nível --}}
                <div class="col-auto text-center">
                    <div class="bg-warning bg-opacity-10 border border-warning rounded-3 px-4 py-2">
                        <div class="text-warning fw-bold" style="font-size:1.8rem; line-height:1;">
                            {{ $nivel }}
                        </div>
                        <small class="text-muted">Nível</small>
                    </div>
                </div>

            </div>

            {{-- Barra de progresso de XP --}}
            <div class="mt-4">
                <div class="d-flex justify-content-between mb-1">
                    <small class="text-muted fw-semibold">Progresso no nível {{ $nivel }}</small>
                    <small class="text-muted">{{ $xpNoNivel }} / {{ $xpParaSubir }} XP</small>
                </div>
                <div class="progress mb-1" style="height:10px;" role="progressbar"
                     aria-valuenow="{{ $progresso }}" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar bg-warning" style="width:{{ $progresso }}%"></div>
                </div>
                <small class="text-muted">XP total: <strong>{{ $xp }}</strong></small>
            </div>
        </div>
    </div>

    {{-- Estatísticas --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body py-4">
                    <div class="display-6 fw-bold text-primary">{{ $usuario->notas->count() }}</div>
                    <div class="text-muted mt-1 small">Avaliações feitas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body py-4">
                    <div class="display-6 fw-bold text-success">{{ $usuario->estantes->count() }}</div>
                    <div class="text-muted mt-1 small">Bibliotecas</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body py-4">
                    <div class="display-6 fw-bold text-warning">{{ $usuario->conquistas->count() }}</div>
                    <div class="text-muted mt-1 small">Conquistas</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Seção de conquistas --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">🏆 Conquistas</h5>
        </div>
        <div class="card-body">
            @if ($usuario->conquistas->isEmpty())
                <p class="text-muted mb-0">Nenhuma conquista desbloqueada ainda. Continue lendo!</p>
            @else
                <div class="row g-3">
                    @foreach ($usuario->conquistas as $conquista)
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="d-flex align-items-start gap-3 p-3 border rounded-3 h-100">
                                <div class="fs-3">🏅</div>
                                <div>
                                    <div class="fw-semibold">{{ $conquista->nome }}</div>
                                    <small class="text-muted">{{ $conquista->descricao }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

@endsection
