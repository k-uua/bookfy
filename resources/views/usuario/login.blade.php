@extends('layouts.app')
@section('titulo', 'Entrar')
@section('conteudo')

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-5">

            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <h2 class="card-title mb-1 fw-bold">Entrar</h2>
                    <p class="text-muted mb-4">
                        Não tem conta?
                        <a href="{{ route('usuario.registro') }}">Registre-se</a>
                    </p>

                    <form action="{{ route('usuario.autenticar') }}" method="post" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                class="form-control @error('email') is-invalid @enderror"
                                placeholder="seu@email.com"
                                autofocus
                                required
                            >
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Senha</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="form-control @error('password') is-invalid @enderror"
                                placeholder="••••••••"
                                required
                            >
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4 form-check">
                            <input
                                type="checkbox"
                                id="lembrar"
                                name="lembrar"
                                value="1"
                                class="form-check-input"
                                {{ old('lembrar') ? 'checked' : '' }}
                            >
                            <label for="lembrar" class="form-check-label">Lembrar de mim</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection
