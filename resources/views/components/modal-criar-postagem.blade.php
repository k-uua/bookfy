@props([
    'id'    => 'criarPostagemModal',
    'livro' => null,   // se passado, pré-associa a postagem ao livro local
])

<div id="{{ $id }}"
     class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-sm px-4 py-8"
     data-modal-backdrop>

    <div class="bg-[#161616] border border-zinc-800 rounded-2xl w-full max-w-xl p-6 sm:p-7 shadow-2xl
                max-h-[90vh] overflow-y-auto">

        <h3 class="text-lg font-semibold text-white mb-1">Nova postagem</h3>
        <p class="text-zinc-400 text-sm mb-5">
            Compartilhe uma opinião, recomendação ou descoberta com a comunidade.
        </p>

        <form action="{{ route('postagens.store') }}" method="post" novalidate>
            @csrf

            {{-- Livro pré-associado (opcional, vem como prop) --}}
            @if ($livro)
                <input type="hidden" name="id_livro" value="{{ $livro->id }}">

                <div class="flex items-center gap-3 bg-[#0d0d0d] border border-zinc-800/60
                            rounded-xl p-3 mb-5">
                    @if ($livro->capa_livro_url)
                        <img src="{{ $livro->capa_livro_url }}" alt="{{ $livro->titulo }}"
                             class="w-10 h-14 object-cover rounded-md shrink-0">
                    @else
                        <div class="w-10 h-14 rounded-md bg-zinc-800 flex items-center justify-center
                                    text-zinc-600 text-xl shrink-0">📖</div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-[10px] uppercase tracking-wider text-zinc-500 mb-0.5">
                            Postando sobre
                        </p>
                        <p class="text-white text-sm font-semibold truncate">{{ $livro->titulo }}</p>
                    </div>
                </div>
            @endif

            {{-- Título --}}
            <div class="mb-4">
                <label for="{{ $id }}-titulo" class="block text-xs text-zinc-500 uppercase tracking-wider mb-2">
                    Título
                </label>
                <input
                    type="text"
                    id="{{ $id }}-titulo"
                    name="titulo"
                    value="{{ old('titulo') }}"
                    placeholder="Dê um título marcante à sua postagem"
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

            {{-- Conteúdo --}}
            <div class="mb-5">
                <label for="{{ $id }}-conteudo" class="block text-xs text-zinc-500 uppercase tracking-wider mb-2">
                    Conteúdo
                </label>
                <textarea
                    id="{{ $id }}-conteudo"
                    name="conteudo"
                    rows="6"
                    placeholder="Escreva sua postagem..."
                    maxlength="5000"
                    minlength="5"
                    required
                    @class([
                        'w-full bg-[#0d0d0d] border rounded-lg px-3.5 py-2.5 resize-none',
                        'text-sm text-white placeholder-zinc-500 outline-none transition-colors',
                        'focus:ring-1',
                        'border-zinc-700 focus:border-blue-500 focus:ring-blue-500/40' => !$errors->has('conteudo'),
                        'border-red-500/70 focus:border-red-500 focus:ring-red-500/40' => $errors->has('conteudo'),
                    ])
                >{{ old('conteudo') }}</textarea>
                @error('conteudo')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" data-modal-close="{{ $id }}"
                        class="text-sm text-zinc-400 hover:text-white transition-colors px-4 py-2">
                    Cancelar
                </button>
                <x-primary-button type="submit">
                    Publicar
                </x-primary-button>
            </div>
        </form>
    </div>
</div>

{{-- Reabre o modal automaticamente em caso de erro de validação --}}
@if ($errors->any() && (old('titulo') !== null || old('conteudo') !== null))
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
