@extends('layouts.main')
@section('titulo', 'Registrar')
@section('conteudo')

<x-auth-card titulo="Criar conta">
    <x-slot:subtitulo>
        Já tem conta?
        <a href="{{ route('usuario.login') }}"
           class="text-blue-400 hover:text-blue-300 transition-colors font-medium">
            Entrar
        </a>
    </x-slot:subtitulo>

    <form action="{{ route('usuario.registrar') }}" method="post" novalidate class="space-y-4">
        @csrf

        <x-input-field
            name="nome"
            label="Nome"
            placeholder="Seu nome"
            autofocus
            required
        />

        <x-input-field
            name="email"
            type="email"
            label="E-mail"
            placeholder="seu@email.com"
            required
        />

        <x-input-field
            name="senha"
            type="password"
            label="Senha"
            placeholder="Mínimo 8 caracteres"
            required
        />

        <x-input-field
            name="senha_confirmation"
            type="password"
            label="Confirmar senha"
            placeholder="Repita a senha"
            required
        />

        <x-primary-button block class="mt-2">
            Criar conta
        </x-primary-button>
    </form>
</x-auth-card>

@endsection
