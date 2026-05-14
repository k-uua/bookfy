@props([
    'id'    => 'criarTopicoModal',
    'forum',
])

<div id="{{ $id }}"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-8"
     data-modal-backdrop>

    <div class="bg-[#161616] border border-zinc-800 rounded-2xl w-full max-w-md p-6 sm:p-7 shadow-2xl">

        <h3 class="text-lg font-semibold text-white mb-1">Novo tópico</h3>
        <p class="text-zinc-400 text-sm mb-5">
            Inicie uma nova discussão em <strong class="text-zinc-200">{{ $forum->nome }}</strong>.
        </p>

        <form action="{{ route('forum.topicos.store', $forum) }}" method="post" novalidate>
            @csrf

            <div class="mb-5">
                <label for="{{ $id }}-titulo" class="block text-xs text-zinc-500 uppercase tracking-wider mb-2">
                    Título
                </label>
                <input
                    type="text"
                    id="{{ $id }}-titulo"
                    name="titulo"
                    value="{{ old('titulo') }}"
                    placeholder="Ex: Qual o melhor livro de fantasia para começar?"
                    maxlength="255"
                    minlength="3"
                    required
                    @class([
                        'w-full bg-[#0d0d0d] border rounded-lg px-3.5 py-2.5',
                        'text-sm text-white placeholder-zinc-500 outline-none transition-colors',
                        'focus:ring-1',
                        'border-zinc-700 focus:border-blue-500 focus:ring-blue-500/40' => !$errors->has('titulo'),
                        'border-red-500/70 focus:border-red-500 focus:ring-red-500/40' => $errors->has('titulo'),
                    ])
                >
                @error('titulo')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" data-modal-close="{{ $id }}"
                        class="text-sm text-zinc-400 hover:text-white transition-colors px-4 py-2">
                    Cancelar
                </button>
                <x-primary-button type="submit">
                    Criar tópico
                </x-primary-button>
            </div>
        </form>
    </div>
</div>

{{-- Reabre o modal automaticamente se houver erro de validação no envio anterior --}}
@if ($errors->any() && old('titulo') !== null)
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
