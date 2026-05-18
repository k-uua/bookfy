<header class="sticky top-0 z-50 bg-[#0d0d0d]/95 backdrop-blur border-b border-zinc-800">
    <nav class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-6">

        @php
            $rotaInicio = Auth::check() ? route('home') : route('livros.index');
            $inicioAtivo = request()->routeIs('home', 'livros.index');
        @endphp

        {{-- Logo --}}
        <a href="{{ $rotaInicio }}"
           class="text-xl font-bold text-white tracking-tight shrink-0 hover:opacity-90 transition-opacity">
            Bookfy
        </a>

        {{-- Links centrais — absolutos para ficarem na metade exata da página --}}
        <ul class="hidden md:flex absolute left-1/2 -translate-x-1/2 items-center gap-7 text-sm font-medium">
            <li>
                <a href="{{ $rotaInicio }}"
                   class="transition-colors {{ $inicioAtivo ? 'text-white' : 'text-zinc-400 hover:text-white' }}">
                    Início
                </a>
            </li>
            <li>
                <a href="{{ Auth::check() ? route('postagens.index') : route('usuario.login') }}"
                   class="transition-colors {{ request()->routeIs('postagens.*') ? 'text-white' : 'text-zinc-400 hover:text-white' }}">
                    Feed
                </a>
            </li>
            <li>
                <a href="#" class="text-zinc-400 hover:text-white transition-colors">Livros</a>
            </li>
            @auth
            <li>
                <a href="{{ route('estante.index') }}"
                   class="transition-colors {{ request()->routeIs('estante.*') ? 'text-white' : 'text-zinc-400 hover:text-white' }}">
                    Biblioteca Pessoal
                </a>
            </li>
            @endauth
        </ul>

        {{-- Lado direito --}}
        <div class="flex items-center gap-3">

            {{-- Barra de busca --}}
            <form action="{{ route('livros.buscar') }}" method="get"
                  class="hidden sm:flex items-center gap-2 bg-zinc-800/80 border border-zinc-700 rounded-full
                         px-3 py-1.5 focus-within:border-blue-500 focus-within:ring-1 focus-within:ring-blue-500/40
                         transition-all">
                <svg class="w-4 h-4 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7" stroke-width="2"/>
                    <path d="M21 21l-3.5-3.5" stroke-linecap="round" stroke-width="2"/>
                </svg>
                <input type="search" name="buscar"
                       placeholder="Buscar livros..."
                       value="{{ request('buscar') }}"
                       class="bg-transparent text-sm text-white placeholder-zinc-500 outline-none w-36
                              focus:w-48 transition-all duration-300 min-w-0">
            </form>
<!-- 
            {{-- Sininho --}}
            <button type="button"
                    class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-400
                           hover:text-white hover:bg-zinc-800 transition-colors"
                    aria-label="Notificações">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </button> -->

            {{-- Usuário / Login --}}
            @guest
                <a href="{{ route('usuario.login') }}"
                   class="w-9 h-9 flex items-center justify-center rounded-full text-zinc-400
                          hover:text-white hover:bg-zinc-800 transition-colors"
                   aria-label="Entrar">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </a>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ route('usuario.perfil') }}"
                       class="flex items-center gap-2 text-sm text-zinc-300 hover:text-white transition-colors">
                        <x-avatar :usuario="Auth::user()" size="md" class="bg-blue-600" />
                    </a>
                    <form action="{{ route('usuario.logout') }}" method="post" class="hidden md:block">
                        @csrf
                        <button type="submit"
                                class="text-xs text-zinc-500 hover:text-white transition-colors">
                            Sair
                        </button>
                    </form>
                </div>
            @endguest

            {{-- Botão menu mobile --}}
            <button id="btnMobileMenu"
                    class="md:hidden w-9 h-9 flex items-center justify-center rounded-full
                           text-zinc-400 hover:text-white hover:bg-zinc-800 transition-colors"
                    aria-label="Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </nav>

    {{-- Menu Mobile --}}
    <div id="mobileMenu"
         class="hidden md:hidden border-t border-zinc-800 bg-[#0d0d0d] px-4 pt-3 pb-4 space-y-1">
        <a href="{{ $rotaInicio }}"
           class="block text-sm py-2.5 px-3 rounded-lg text-zinc-300 hover:text-white hover:bg-zinc-800 transition-colors">
            Início
        </a>
        <a href="{{ Auth::check() ? route('postagens.index') : route('usuario.login') }}"
           class="block text-sm py-2.5 px-3 rounded-lg text-zinc-300 hover:text-white hover:bg-zinc-800 transition-colors">
            Feed
        </a>
        <a href="#"
           class="block text-sm py-2.5 px-3 rounded-lg text-zinc-300 hover:text-white hover:bg-zinc-800 transition-colors">
            Livros
        </a>
        @auth
            <a href="{{ route('estante.index') }}"
               class="block text-sm py-2.5 px-3 rounded-lg text-zinc-300 hover:text-white hover:bg-zinc-800 transition-colors">
                Biblioteca Pessoal
            </a>
            <form action="{{ route('usuario.logout') }}" method="post" class="pt-1">
                @csrf
                <button type="submit"
                        class="w-full text-left text-sm py-2.5 px-3 rounded-lg text-zinc-400
                               hover:text-white hover:bg-zinc-800 transition-colors">
                    Sair
                </button>
            </form>
        @else
            <div class="flex gap-2 pt-2">
                <a href="{{ route('usuario.login') }}"
                   class="flex-1 text-center text-sm py-2 rounded-lg border border-zinc-700
                          text-zinc-300 hover:border-white hover:text-white transition-colors">
                    Entrar
                </a>
                <a href="{{ route('usuario.registro') }}"
                   class="flex-1 text-center text-sm py-2 rounded-lg bg-blue-600
                          hover:bg-blue-700 text-white font-medium transition-colors">
                    Registrar
                </a>
            </div>
        @endguest

        <form action="{{ route('livros.buscar') }}" method="get"
              class="flex items-center gap-2 bg-zinc-800 border border-zinc-700 rounded-full px-3 py-2 mt-2">
            <svg class="w-4 h-4 text-zinc-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <circle cx="11" cy="11" r="7" stroke-width="2"/>
                <path d="M21 21l-3.5-3.5" stroke-linecap="round" stroke-width="2"/>
            </svg>
            <input type="search" name="buscar" placeholder="Buscar livros..."
                   class="flex-1 bg-transparent text-sm text-white placeholder-zinc-500 outline-none">
        </form>
    </div>
</header>

<script>
    (function () {
        const btn = document.getElementById('btnMobileMenu');
        const nav = document.getElementById('mobileMenu');
        if (btn && nav) {
            btn.addEventListener('click', () => nav.classList.toggle('hidden'));
        }
    })();
</script>
