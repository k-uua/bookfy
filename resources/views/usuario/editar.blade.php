@extends('layouts.main')
@section('titulo', 'Editar perfil')
@section('conteudo')

<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    {{-- Voltar --}}
    <a href="{{ route('usuario.perfil') }}"
       class="inline-flex items-center gap-1.5 text-zinc-400 hover:text-white text-sm transition-colors mb-8">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Voltar ao perfil
    </a>

    <h1 class="text-white text-2xl font-bold tracking-tight mb-8">Editar perfil</h1>

    {{-- ══════════════════════════════════════════════════
         FOTO DE PERFIL
    ══════════════════════════════════════════════════ --}}
    <section class="bg-zinc-900/60 border border-zinc-800 rounded-2xl p-6 mb-5">
        <h2 class="text-white text-sm font-semibold mb-5">Foto de perfil</h2>

        <div class="flex items-center gap-6">

            {{-- Avatar com overlay de câmera --}}
            <form action="{{ route('usuario.foto') }}" method="post"
                  enctype="multipart/form-data" id="form-foto" class="shrink-0">
                @csrf
                <input type="file" name="foto" id="foto-input"
                       accept="image/jpeg,image/png,image/webp"
                       class="sr-only"
                       onchange="document.getElementById('form-foto').submit()">

                <label for="foto-input"
                       class="relative group/avatar cursor-pointer block w-20 h-20"
                       aria-label="Alterar foto de perfil">
                    <x-avatar :usuario="$usuario" size="2xl"
                              class="bg-zinc-700 ring-4 ring-zinc-800" />

                    <div class="absolute inset-0 rounded-full flex items-center justify-center
                                bg-black/50 opacity-0 group-hover/avatar:opacity-100 transition-opacity">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </label>
            </form>

            {{-- Instruções + remover --}}
            <div class="min-w-0">
                <p class="text-zinc-300 text-sm font-medium">{{ $usuario->nome }}</p>
                <p class="text-zinc-500 text-xs mt-0.5 mb-3">
                    Clique na foto para trocar &bull; JPEG, PNG ou WebP &bull; máx. 2 MB
                </p>

                @if ($usuario->foto_perfil)
                    <form action="{{ route('usuario.foto.remover') }}" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-red-400 hover:text-red-300 text-xs transition-colors"
                                onclick="return confirm('Remover foto de perfil?')">
                            Remover foto
                        </button>
                    </form>
                @endif

                @error('foto')
                    <p class="text-red-400 text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════
         DADOS PESSOAIS
    ══════════════════════════════════════════════════ --}}
    <form action="{{ route('usuario.atualizar') }}" method="post" novalidate>
        @csrf
        @method('PUT')

        <section class="bg-zinc-900/60 border border-zinc-800 rounded-2xl p-6 mb-5">
            <h2 class="text-white text-sm font-semibold mb-5">Dados pessoais</h2>

            <div class="space-y-4">
                <x-input-field
                    name="nome"
                    label="Nome"
                    placeholder="Seu nome"
                    :value="$usuario->nome"
                    required
                />
                <x-input-field
                    name="email"
                    label="E-mail"
                    type="email"
                    placeholder="seu@email.com"
                    :value="$usuario->email"
                    required
                />
            </div>
        </section>

        {{-- ══════════════════════════════════════════════════
             ALTERAR SENHA (OPCIONAL)
        ══════════════════════════════════════════════════ --}}
        <section class="bg-zinc-900/60 border border-zinc-800 rounded-2xl p-6 mb-8">
            <h2 class="text-white text-sm font-semibold mb-1">Alterar senha</h2>
            <p class="text-zinc-500 text-xs mb-5">Deixe em branco para manter a senha atual.</p>

            <div class="space-y-4">
                <x-input-field
                    name="senha_atual"
                    label="Senha atual"
                    type="password"
                    placeholder="••••••••"
                />
                <x-input-field
                    name="nova_senha"
                    label="Nova senha"
                    type="password"
                    placeholder="Mínimo 8 caracteres"
                />
                <x-input-field
                    name="nova_senha_confirmation"
                    label="Confirmar nova senha"
                    type="password"
                    placeholder="Repita a nova senha"
                />
            </div>
        </section>

        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('usuario.perfil') }}"
               class="px-5 py-2.5 text-sm text-zinc-400 hover:text-white transition-colors">
                Cancelar
            </a>
            <x-primary-button>
                Salvar alterações
            </x-primary-button>
        </div>
    </form>

</div>

@endsection
