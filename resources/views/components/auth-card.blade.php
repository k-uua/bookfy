@props(['titulo'])

<div class="flex items-center justify-center px-4 py-16 sm:py-20"
     style="min-height: calc(100vh - 280px);">

    <div class="w-full max-w-md">
        <div class="bg-[#161616] border border-zinc-800/60 rounded-2xl shadow-2xl shadow-black/40 p-8 sm:p-10">

            <h1 class="text-2xl font-bold text-white">{{ $titulo }}</h1>

            @isset($subtitulo)
                <p class="text-sm text-zinc-400 mt-1.5 mb-6">
                    {{ $subtitulo }}
                </p>
            @else
                <div class="mb-6"></div>
            @endisset

            {{ $slot }}
        </div>
    </div>

</div>
