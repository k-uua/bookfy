<footer class="border-t border-zinc-800 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-8">

            <div>
                <h3 class="text-white font-semibold text-sm mb-4">Home</h3>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="{{ route('livros.index') }}" class="hover:text-white transition-colors">Início</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Categorias</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Tendências</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Preços</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">FAQ</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold text-sm mb-4">Livros</h3>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="#" class="hover:text-white transition-colors">Gêneros</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Tendências</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Lançamentos</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Populares</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold text-sm mb-4">Comunidade</h3>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="#" class="hover:text-white transition-colors">Fóruns</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Discussões</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Resenhas</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Listas</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold text-sm mb-4">Suporte</h3>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="#" class="hover:text-white transition-colors">Fale Conosco</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Central de Ajuda</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold text-sm mb-4">Assinatura</h3>
                <ul class="space-y-2 text-sm text-zinc-400">
                    <li><a href="#" class="hover:text-white transition-colors">Planos</a></li>
                    <li><a href="#" class="hover:text-white transition-colors">Recursos</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold text-sm mb-4">Redes Sociais</h3>
                <div class="flex gap-2">
                    <a href="#"
                       class="w-9 h-9 rounded-full bg-zinc-800 hover:bg-zinc-700 flex items-center
                              justify-center transition-colors"
                       aria-label="Facebook">
                        <svg class="w-4 h-4 text-zinc-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#"
                       class="w-9 h-9 rounded-full bg-zinc-800 hover:bg-zinc-700 flex items-center
                              justify-center transition-colors"
                       aria-label="Twitter">
                        <svg class="w-4 h-4 text-zinc-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                        </svg>
                    </a>
                    <a href="#"
                       class="w-9 h-9 rounded-full bg-zinc-800 hover:bg-zinc-700 flex items-center
                              justify-center transition-colors"
                       aria-label="LinkedIn">
                        <svg class="w-4 h-4 text-zinc-300" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <div class="border-t border-zinc-800 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8
                    flex flex-col sm:flex-row items-center justify-between gap-2">
            <span class="text-xs text-zinc-500">
                &copy; {{ date('Y') }} Bookfy. Todos os direitos reservados.
            </span>
            <div class="flex gap-4 text-xs text-zinc-500">
                <a href="#" class="hover:text-zinc-300 transition-colors">Política de Privacidade</a>
                <a href="#" class="hover:text-zinc-300 transition-colors">Política de Cookies</a>
            </div>
        </div>
    </div>
</footer>
