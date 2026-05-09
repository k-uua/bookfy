@if (session('success') || session('info') || $errors->any())
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full space-y-2">
        @if (session('success'))
            <div class="flex items-center justify-between gap-4 rounded-xl border border-green-700/60
                        bg-green-950/60 px-4 py-3 text-sm text-green-300">
                <span>{{ session('success') }}</span>
                <button type="button" onclick="this.parentElement.remove()"
                        class="shrink-0 text-green-500 hover:text-white transition-colors text-lg leading-none">
                    &times;
                </button>
            </div>
        @endif

        @if (session('info'))
            <div class="flex items-center justify-between gap-4 rounded-xl border border-blue-700/60
                        bg-blue-950/60 px-4 py-3 text-sm text-blue-300">
                <span>{{ session('info') }}</span>
                <button type="button" onclick="this.parentElement.remove()"
                        class="shrink-0 text-blue-500 hover:text-white transition-colors text-lg leading-none">
                    &times;
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="flex items-start justify-between gap-4 rounded-xl border border-red-700/60
                        bg-red-950/60 px-4 py-3 text-sm text-red-300">
                <ul class="space-y-0.5 list-none m-0 p-0">
                    @foreach ($errors->all() as $erro)
                        <li>{{ $erro }}</li>
                    @endforeach
                </ul>
                <button type="button" onclick="this.parentElement.remove()"
                        class="shrink-0 text-red-500 hover:text-white transition-colors text-lg leading-none">
                    &times;
                </button>
            </div>
        @endif
    </div>
@endif
