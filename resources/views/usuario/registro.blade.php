@extends('layouts.app')
@section('titulo', 'Registrar')
@section('conteudo')

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-5">

            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h2 class="card-title mb-1 fw-bold">Criar conta</h2>
                    <p class="text-muted mb-4">
                        Já tem conta?
                        <a href="{{ route('usuario.login') }}">Entrar</a>
                    </p>

                    <form action="{{ route('usuario.registrar') }}" method="post" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input
                                type="text"
                                id="nome"
                                name="nome"
                                value="{{ old('nome') }}"
                                class="form-control @error('nome') is-invalid @enderror"
                                placeholder="Seu nome"
                                autofocus
                                required
                            >
                            @error('nome')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="seu@email.com"
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <input
                                type="password"
                                id="senha"
                                name="senha"
                                class="form-control @error('senha') is-invalid @enderror"
                                placeholder="Mínimo 8 caracteres"
                                required
                            >
                            @error('senha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="senha_confirmation" class="form-label">Confirmar senha</label>
                            <input
                                type="password"
                                id="senha_confirmation"
                                name="senha_confirmation"
                                class="form-control"
                                placeholder="Repita a senha"
                                required
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Criar conta</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
