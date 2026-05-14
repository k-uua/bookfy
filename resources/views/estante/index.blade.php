@extends('layouts.main')
@section('titulo', 'Minhas Estantes')
@section('conteudo')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between gap-3 mb-8 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-white">Minhas Estantes</h1>
            <p class="text-zinc-500 text-sm mt-1">
                {{ $estantes->count() }} {{ Str::plural('estante', $estantes->count()) }}
            </p>
        </div>

        <button type="button" data-modal-open="criarEstanteModal"
                class="flex items-center gap-2 bg-blue-600 hover:bg-blue-500 active:bg-blue-700
                       text-white text-sm font-semibold px-4 py-2.5 rounded-lg
                       shadow-lg shadow-blue-900/30 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            Nova estante
        </button>
    </div>

    @if ($estantes->isEmpty())

        {{-- ──── Estado vazio ──── --}}
        <div class="text-center py-20">
            <div class="w-16 h-16 rounded-full bg-zinc-800 flex items-center justify-center mx-auto mb-4 text-3xl">
                📚
            </div>
            <p class="text-zinc-200 text-lg font-medium mb-1">Você ainda não tem estantes</p>
            <p class="text-zinc-500 text-sm mb-6">
                Crie uma estante vazia ou adicione um livro a partir da página de detalhes.
            </p>
            <button type="button" data-modal-open="criarEstanteModal"
                    class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm
                           font-semibold px-6 py-2.5 rounded-lg transition-colors shadow-lg shadow-blue-900/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Criar primeira estante
            </button>
        </div>

    @else

        {{-- ──── Grid de estantes ──── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
            @foreach ($estantes as $estante)
                <div class="bg-[#161616] border border-zinc-800/60 hover:border-zinc-700
                            rounded-2xl overflow-hidden transition-all
                            hover:-translate-y-1 hover:shadow-xl hover:shadow-black/40 flex flex-col">

                    {{-- Área clicável (cover + info) --}}
                    <a href="{{ route('estante.show', $estante) }}" class="block group">
                        <div class="relative aspect-[16/9]
                                    bg-gradient-to-br from-indigo-600 via-violet-600 to-purple-700
                                    flex items-center justify-center overflow-hidden">
                            <span class="text-5xl drop-shadow-lg">📚</span>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                        </div>

                        <div class="px-4 pt-3 pb-3">
                            <h3 class="font-semibold text-white text-base truncate
                                       group-hover:text-blue-400 transition-colors">
                                {{ $estante->nome }}
                            </h3>
                            <p class="text-zinc-500 text-xs mt-0.5">
                                {{ $estante->livros_count }} {{ Str::plural('livro', $estante->livros_count) }}
                            </p>
                        </div>
                    </a>

                    {{-- Rodapé com ações --}}
                    <div class="flex divide-x divide-zinc-800/60 border-t border-zinc-800/60 mt-auto">
                        <button type="button"
                                data-action="renomear-estante"
                                data-estante-id="{{ $estante->id }}"
                                data-estante-nome="{{ $estante->nome }}"
                                class="flex-1 py-2.5 text-xs text-zinc-400 hover:bg-zinc-800
                                       hover:text-white transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Renomear
                        </button>
                        <button type="button"
                                data-action="excluir-estante"
                                data-estante-id="{{ $estante->id }}"
                                data-estante-nome="{{ $estante->nome }}"
                                data-livros-count="{{ $estante->livros_count }}"
                                class="flex-1 py-2.5 text-xs text-zinc-500 hover:bg-red-950/40
                                       hover:text-red-300 transition-colors flex items-center justify-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3"/>
                            </svg>
                            Excluir
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

    @endif

</div>

{{-- ═══════════════════════════════════════════════════════════════
     Modal · Criar nova estante
════════════════════════════════════════════════════════════════ --}}
<div id="criarEstanteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-8"
     data-modal-backdrop>

    <div class="bg-[#161616] border border-zinc-800 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <h3 class="text-lg font-semibold text-white mb-1">Nova estante</h3>
        <p class="text-zinc-400 text-sm mb-5">
            Dê um nome para sua estante. Você pode adicionar livros depois.
        </p>

        <form action="{{ route('estante.store') }}" method="post">
            @csrf
            <label for="nomeCriar" class="block text-xs text-zinc-500 uppercase tracking-wider mb-2">
                Nome
            </label>
            <input type="text" id="nomeCriar" name="nome"
                   placeholder="Ex: Favoritos, Lendo, Quero ler..."
                   maxlength="255" required minlength="2"
                   class="w-full bg-[#0d0d0d] border border-zinc-700 rounded-lg px-3.5 py-2.5
                          text-sm text-white placeholder-zinc-500 outline-none transition-colors
                          focus:border-blue-500 focus:ring-1 focus:ring-blue-500/40">

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" data-modal-close="criarEstanteModal"
                        class="text-sm text-zinc-400 hover:text-white transition-colors px-4 py-2">
                    Cancelar
                </button>
                <x-primary-button type="submit">
                    Criar estante
                </x-primary-button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     Modal · Renomear estante (compartilhado, populado via JS)
════════════════════════════════════════════════════════════════ --}}
<div id="renomearEstanteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-8"
     data-modal-backdrop>

    <div class="bg-[#161616] border border-zinc-800 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <h3 class="text-lg font-semibold text-white mb-1">Renomear estante</h3>
        <p class="text-zinc-400 text-sm mb-5">
            Escolha um novo nome para esta estante.
        </p>

        <form id="renomearEstanteForm" action="" method="post">
            @csrf
            @method('PATCH')
            <label for="nomeRenomear" class="block text-xs text-zinc-500 uppercase tracking-wider mb-2">
                Nome
            </label>
            <input type="text" id="nomeRenomear" name="nome"
                   maxlength="255" required minlength="2"
                   class="w-full bg-[#0d0d0d] border border-zinc-700 rounded-lg px-3.5 py-2.5
                          text-sm text-white placeholder-zinc-500 outline-none transition-colors
                          focus:border-blue-500 focus:ring-1 focus:ring-blue-500/40">

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" data-modal-close="renomearEstanteModal"
                        class="text-sm text-zinc-400 hover:text-white transition-colors px-4 py-2">
                    Cancelar
                </button>
                <x-primary-button type="submit">
                    Salvar
                </x-primary-button>
            </div>
        </form>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
     Modal · Excluir estante (texto dinâmico baseado em quantidade)
════════════════════════════════════════════════════════════════ --}}
<div id="excluirEstanteModal"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-8"
     data-modal-backdrop>

    <div class="bg-[#161616] border border-zinc-800 rounded-2xl w-full max-w-md p-6 shadow-2xl">
        <div class="flex items-start gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-red-950/60 border border-red-900/60
                        flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-semibold text-white">Excluir estante</h3>
                <p class="text-zinc-400 text-sm mt-1">
                    Tem certeza que deseja excluir a estante
                    <strong class="text-white" data-estante-nome></strong>?
                </p>
            </div>
        </div>

        {{-- Aviso quando há livros (escondido por padrão) --}}
        <div data-aviso-livros class="hidden bg-amber-950/40 border border-amber-900/60 rounded-lg p-3 mb-4">
            <p class="text-amber-300 text-xs leading-relaxed">
                ⚠️ Esta estante contém <strong data-livros-count></strong>
                <span data-livros-plural>livros</span>. Eles permanecerão no sistema,
                mas serão removidos desta estante.
            </p>
        </div>

        <form id="excluirEstanteForm" action="" method="post">
            @csrf
            @method('DELETE')
            <div class="flex justify-end gap-2">
                <button type="button" data-modal-close="excluirEstanteModal"
                        class="text-sm text-zinc-400 hover:text-white transition-colors px-4 py-2">
                    Cancelar
                </button>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-500 active:bg-red-700 text-white
                               text-sm font-semibold px-6 py-2.5 rounded-lg transition-colors
                               shadow-lg shadow-red-900/30">
                    Excluir
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    (function () {
        // Listener para os botões "Renomear" e "Excluir" de cada card.
        // Os modais são compartilhados — populamos action/conteúdo via dataset.
        document.addEventListener('click', (e) => {

            // ── Renomear ──────────────────────────────────────────────────────
            const renomearBtn = e.target.closest('[data-action="renomear-estante"]');
            if (renomearBtn) {
                const id   = renomearBtn.dataset.estanteId;
                const nome = renomearBtn.dataset.estanteNome;

                const form  = document.getElementById('renomearEstanteForm');
                const input = document.getElementById('nomeRenomear');
                const modal = document.getElementById('renomearEstanteModal');

                form.action = `/estantes/${id}`;
                input.value = nome;

                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => { input.focus(); input.select(); }, 50);
                return;
            }

            // ── Excluir ───────────────────────────────────────────────────────
            const excluirBtn = e.target.closest('[data-action="excluir-estante"]');
            if (excluirBtn) {
                const id    = excluirBtn.dataset.estanteId;
                const nome  = excluirBtn.dataset.estanteNome;
                const count = parseInt(excluirBtn.dataset.livrosCount || '0', 10);

                const form        = document.getElementById('excluirEstanteForm');
                const modal       = document.getElementById('excluirEstanteModal');
                const nomeSpan    = modal.querySelector('[data-estante-nome]');
                const aviso       = modal.querySelector('[data-aviso-livros]');
                const countEl     = modal.querySelector('[data-livros-count]');
                const pluralEl    = modal.querySelector('[data-livros-plural]');

                form.action = `/estantes/${id}`;
                nomeSpan.textContent = `"${nome}"`;

                if (count > 0) {
                    countEl.textContent = count;
                    pluralEl.textContent = count === 1 ? 'livro' : 'livros';
                    aviso.classList.remove('hidden');
                } else {
                    aviso.classList.add('hidden');
                }

                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        });
    })();
</script>
@endpush
