@extends('layouts.main')
@section('titulo', 'Entrar')
@section('conteudo')

<x-auth-card titulo="Entrar">
    <x-slot:subtitulo>
        Não tem conta?
        <a href="{{ route('usuario.registro') }}"
           class="text-blue-400 hover:text-blue-300 transition-colors font-medium">
            Registre-se
        </a>
    </x-slot:subtitulo>

    <form action="{{ route('usuario.autenticar') }}" method="post" novalidate class="space-y-4">
        @csrf

        <x-input-field
            name="email"
            type="email"
            label="E-mail"
            placeholder="seu@email.com"
            autofocus
            required
        />

        <x-input-field
            name="password"
            type="password"
            label="Senha"
            placeholder="••••••••"
            required
        />

        <label class="flex items-center gap-2 text-sm text-zinc-400 cursor-pointer select-none pt-1">
            <input
                type="checkbox"
                name="lembrar"
                value="1"
                {{ old('lembrar') ? 'checked' : '' }}
                class="w-4 h-4 rounded border-zinc-600 bg-[#1c1c1e] text-blue-600
                       focus:ring-1 focus:ring-blue-500/40 focus:ring-offset-0"
            >
            Lembrar de mim
        </label>

        <x-primary-button block class="mt-2">
            Entrar
        </x-primary-button>
    </form>
</x-auth-card>

@endsection
