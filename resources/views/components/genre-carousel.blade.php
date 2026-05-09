@props([
    'guest' => false,         // se true, todos os cards levam para o login
    'id'    => 'genreScroll',
])

@php
$categorias = [
    ['label' => 'Ficção',            'valor' => 'Fiction',         'de' => 'from-violet-950', 'para' => 'to-violet-800',  'icone' => '📚'],
    ['label' => 'Romance',           'valor' => 'Romance',         'de' => 'from-rose-950',   'para' => 'to-rose-700',    'icone' => '💞'],
    ['label' => 'Fantasia',          'valor' => 'Fantasy',         'de' => 'from-amber-950',  'para' => 'to-amber-700',   'icone' => '🧙'],
    ['label' => 'Ficção Científica', 'valor' => 'Science Fiction', 'de' => 'from-cyan-950',   'para' => 'to-cyan-700',    'icone' => '🚀'],
    ['label' => 'Mistério',          'valor' => 'Mystery',         'de' => 'from-slate-900',  'para' => 'to-slate-700',   'icone' => '🔍'],
    ['label' => 'Terror',            'valor' => 'Horror',          'de' => 'from-red-950',    'para' => 'to-red-800',     'icone' => '👁️'],
    ['label' => 'Aventura',          'valor' => 'Adventure',       'de' => 'from-green-950',  'para' => 'to-green-700',   'icone' => '🗺️'],
    ['label' => 'Drama',             'valor' => 'Drama',           'de' => 'from-blue-950',   'para' => 'to-blue-700',    'icone' => '🎭'],
];
@endphp

<section class="py-14 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">

        <x-section-header
            titulo="Navegue pelos gêneros"
            subtitulo="Encontre histórias e novos mundos organizados por gênero e descubra sua próxima leitura"
            :comNav="true"
            :navTarget="$id"
        />

        <div id="{{ $id }}"
             class="flex gap-4 overflow-x-auto pb-2 scroll-smooth scrollbar-hide">

            @foreach ($categorias as $cat)
                @php
                    $cardClasses = "relative w-44 h-52 rounded-2xl overflow-hidden text-left block group
                                    focus:outline-none focus:ring-2 focus:ring-blue-500 shrink-0";
                @endphp

                @if ($guest)
                    {{-- Modo guest: todo card vira link para login --}}
                    <a href="{{ route('usuario.login') }}" class="{{ $cardClasses }}">
                        <x-genre-carousel-card :categoria="$cat" />
                    </a>
                @else
                    {{-- Modo autenticado: form que dispara busca por categoria --}}
                    <form action="{{ route('livros.categorias') }}" method="get" class="shrink-0">
                        <input type="hidden" name="categoria" value="{{ $cat['valor'] }}">
                        <button type="submit" class="{{ $cardClasses }}">
                            <x-genre-carousel-card :categoria="$cat" />
                        </button>
                    </form>
                @endif
            @endforeach

        </div>
    </div>
</section>
