@props([
    'id' => 'criarForumModal',
])

<div id="{{ $id }}"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-8"
     data-modal-backdrop>

    <div class="bg-[#161616] border border-zinc-800 rounded-2xl w-full max-w-lg p-6 sm:p-7 shadow-2xl">

        <h3 class="text-lg font-semibold text-white mb-1">Criar fórum</h3>
        <p class="text-zinc-400 text-sm mb-5">
            Descreva o tema do seu fórum — ele aparecerá na lista de fóruns assim que for criado.
        </p>

        <form action="{{ route('forum.store') }}" method="post" novalidate>
            @csrf

            {{-- Nome --}}
            <div class="mb-4">
                <label for="{{ $id }}-nome" class="block text-xs text-zinc-500 uppercase tracking-wider mb-2">
                    Nome
                </label>
                <input
                    type="text"
                    id="{{ $id }}-nome"
                    name="nome"
                    value="{{ old('nome') }}"
                    placeholder="Ex: Discussões sobre fantasia épica"
                    maxlength="255"
                    minlength="3"
                    required
                    @class([
                        'w-full bg-[#0d0d0d] border rounded-lg px-3.5 py-2.5',
                        'text-sm text-white placeholder-zinc-500 outline-none transition-colors',
                        'focus:ring-1',
                        'border-zinc-700 focus:border-blue-500 focus:ring-blue-500/40' => !$errors->has('nome'),
                        'border-red-500/70 focus:border-red-500 focus:ring-red-500/40' => $errors->has('nome'),
                    ])
                >
                @error('nome')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            {{-- Descrição --}}
            <div class="mb-5">
                <label for="{{ $id }}-descricao" class="block text-xs text-zinc-500 uppercase tracking-wider mb-2">
                    Descrição
                </label>
                <textarea
                    id="{{ $id }}-descricao"
                    name="descricao"
                    rows="4"
                    placeholder="Sobre o que as pessoas vão conversar neste fórum?"
                    maxlength="1000"
                    minlength="10"
                    required
                    @class([
                        'w-full bg-[#0d0d0d] border rounded-lg px-3.5 py-2.5 resize-none',
                        'text-sm text-white placeholder-zinc-500 outline-none transition-colors',
                        'focus:ring-1',
                        'border-zinc-700 focus:border-blue-500 focus:ring-blue-500/40' => !$errors->has('descricao'),
                        'border-red-500/70 focus:border-red-500 focus:ring-red-500/40' => $errors->has('descricao'),
                    ])
                >{{ old('descricao') }}</textarea>
                @error('descricao')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" data-modal-close="{{ $id }}"
                        class="text-sm text-zinc-400 hover:text-white transition-colors px-4 py-2">
                    Cancelar
                </button>
                <x-primary-button type="submit">
                    Criar fórum
                </x-primary-button>
            </div>
        </form>
    </div>
</div>

{{-- Reabre o modal automaticamente quando o servidor retorna erros de validação,
     preservando o que o usuário digitou (via old()). --}}
@if ($errors->any() && (old('nome') !== null || old('descricao') !== null))
    @push('scripts')
    <script>
        (function () {
            const modal = document.getElementById(@json($id));
            if (modal) {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            }
        })();
    </script>
    @endpush
@endif
