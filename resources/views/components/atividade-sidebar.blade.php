@props(['topUsuarios' => collect(), 'topPostagens' => collect()])

@php
    $medalhas = [
        1 => ['cor' => 'text-amber-400',  'icone' => '🥇'],
        2 => ['cor' => 'text-zinc-300',   'icone' => '🥈'],
        3 => ['cor' => 'text-amber-600',  'icone' => '🥉'],
    ];
@endphp

<aside class="space-y-6">

    {{-- ── Usuários em destaque ─────────────────────────────── --}}
    <div class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-5">
        <h2 class="text-sm font-semibold text-zinc-300 uppercase tracking-wider mb-4">
            Usuários em destaque
        </h2>

        @if ($topUsuarios->isEmpty())
            <p class="text-zinc-600 text-sm">Nenhum usuário ainda.</p>
        @else
            <ol class="space-y-3">
                @foreach ($topUsuarios as $i => $usuario)
                    @php $pos = $i + 1; @endphp
                    <li class="flex items-center gap-3">

                        {{-- Posição / medalha --}}
                        <span class="w-6 text-center shrink-0 text-base leading-none">
                            @if (isset($medalhas[$pos]))
                                {{ $medalhas[$pos]['icone'] }}
                            @else
                                <span class="text-zinc-600 text-sm font-semibold">{{ $pos }}</span>
                            @endif
                        </span>

                        {{-- Avatar --}}
                        <x-avatar :usuario="$usuario" size="sm" class="bg-blue-600 shrink-0" />

                        {{-- Nome + métricas --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-white text-sm font-medium truncate leading-tight">
                                {{ $usuario->nome }}
                            </p>
                            <p class="text-zinc-500 text-xs mt-0.5">
                                <span class="text-zinc-400 font-medium">{{ $usuario->total_likes }}</span>
                                {{ Str::plural('like', $usuario->total_likes) }}
                                &middot;
                                <span class="text-zinc-400 font-medium">{{ $usuario->total_posts }}</span>
                                {{ Str::plural('post', $usuario->total_posts) }}
                            </p>
                        </div>

                        {{-- Número de posição colorido para top 3 --}}
                        @if (isset($medalhas[$pos]))
                            <span class="shrink-0 text-xs font-bold {{ $medalhas[$pos]['cor'] }}">#{{ $pos }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

    {{-- ── Posts em alta ──────────────────────────────────────── --}}
    <div class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-5">
        <h2 class="text-sm font-semibold text-zinc-300 uppercase tracking-wider mb-4">
            Posts em alta
        </h2>

        @if ($topPostagens->isEmpty())
            <p class="text-zinc-600 text-sm">Nenhum post ainda.</p>
        @else
            <ol class="space-y-4">
                @foreach ($topPostagens as $i => $post)
                    @php $pos = $i + 1; @endphp
                    <li class="flex gap-3">

                        {{-- Posição / medalha --}}
                        <span class="w-6 text-center shrink-0 text-base leading-none mt-0.5">
                            @if (isset($medalhas[$pos]))
                                {{ $medalhas[$pos]['icone'] }}
                            @else
                                <span class="text-zinc-600 text-sm font-semibold">{{ $pos }}</span>
                            @endif
                        </span>

                        <div class="flex-1 min-w-0">
                            {{-- Autor --}}
                            <div class="flex items-center gap-1.5 mb-1">
                                <x-avatar :usuario="$post->usuario" size="xs" class="bg-blue-600 shrink-0" />
                                <span class="text-zinc-300 text-xs font-medium truncate">
                                    {{ $post->usuario->nome ?? 'Usuário' }}
                                </span>
                                @if (isset($medalhas[$pos]))
                                    <span class="shrink-0 text-xs font-bold {{ $medalhas[$pos]['cor'] }}">#{{ $pos }}</span>
                                @endif
                            </div>

                            {{-- Título --}}
                            <a href="{{ route('postagens.show', $post) }}"
                               class="text-white text-xs font-medium leading-snug line-clamp-2
                                      hover:text-blue-400 transition-colors">
                                {{ $post->titulo }}
                            </a>

                            {{-- Likes --}}
                            <p class="text-zinc-600 text-xs mt-1">
                                <span class="text-zinc-400 font-medium">{{ $post->likes_count }}</span>
                                {{ Str::plural('like', $post->likes_count) }}
                            </p>
                        </div>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

</aside>
