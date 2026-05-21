@extends('layouts.main')
@section('titulo', 'Conquistas — ' . $usuario->nome)

@php
$corNivel = [
    'bronze' => ['text' => 'text-amber-700',  'bg' => 'bg-amber-900/20',  'border' => 'border-amber-800/50',  'glow' => 'shadow-amber-900/40',  'bar' => 'bg-amber-700',  'ring' => 'ring-amber-800/60'],
    'prata'  => ['text' => 'text-zinc-300',   'bg' => 'bg-zinc-700/20',   'border' => 'border-zinc-600/50',   'glow' => 'shadow-zinc-700/40',   'bar' => 'bg-zinc-400',   'ring' => 'ring-zinc-600/60'],
    'ouro'   => ['text' => 'text-amber-400',  'bg' => 'bg-amber-800/20',  'border' => 'border-amber-700/50',  'glow' => 'shadow-amber-800/40',  'bar' => 'bg-amber-400',  'ring' => 'ring-amber-700/60'],
];
$medalha = ['bronze' => '🥉', 'prata' => '🥈', 'ouro' => '🥇'];
$iconeDefault = '<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>';
@endphp

@section('conteudo')

{{-- ═══════════════════════════════════════════════════════════════
     1. HEADER GAMIFICADO
════════════════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden border-b border-zinc-800/60">
    {{-- Glow de fundo --}}
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-32 left-1/2 -translate-x-1/2 w-[600px] h-[300px]
                    bg-blue-600/8 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">

            {{-- Avatar --}}
            <x-avatar :usuario="$usuario" size="xl" class="bg-zinc-700 ring-4 ring-zinc-800 shrink-0" />

            {{-- Nome + rank + XP --}}
            <div class="flex-1 min-w-0 text-center sm:text-left">
                <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1">
                    <h1 class="text-white text-2xl sm:text-3xl font-bold tracking-tight truncate">
                        {{ $usuario->nome }}
                    </h1>
                    <span class="inline-flex items-center gap-1.5 bg-blue-600/15 border border-blue-500/30
                                 rounded-full px-3 py-0.5 text-blue-400 text-xs font-semibold shrink-0">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Lv. {{ $nivel }} &middot; {{ $rank }}
                    </span>
                </div>

                <p class="text-zinc-500 text-sm mb-4">
                    {{ number_format($xp) }} XP total &middot; {{ $xpNoNivel }} / {{ $xpParaSubir }} XP para o próximo nível
                </p>

                {{-- Barra de XP --}}
                <div class="max-w-sm mx-auto sm:mx-0">
                    <div class="flex items-center justify-between text-xs text-zinc-500 mb-1.5">
                        <span>Nível {{ $nivel }}</span>
                        <span>{{ $progressoXp }}%</span>
                        <span>Nível {{ $nivel + 1 }}</span>
                    </div>
                    <div class="h-2.5 bg-zinc-800 rounded-full overflow-hidden ring-1 ring-zinc-700/50"
                         role="progressbar"
                         aria-valuenow="{{ $progressoXp }}"
                         aria-valuemin="0" aria-valuemax="100">
                        <div class="h-full rounded-full transition-all duration-700
                                    bg-gradient-to-r from-blue-600 to-blue-400
                                    shadow-[0_0_8px_rgba(59,130,246,0.6)]"
                             style="width: {{ $progressoXp }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Voltar ao perfil --}}
            <a href="{{ route('usuario.perfil') }}"
               class="hidden sm:flex items-center gap-1.5 text-zinc-400 hover:text-white
                      text-sm transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Voltar ao perfil
            </a>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     2. ESTATÍSTICAS GERAIS
════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">

        <div class="bg-[#161616] border border-zinc-800/60 rounded-2xl p-4 text-center">
            <div class="text-3xl font-bold text-white tabular-nums">{{ $stats['total'] }}</div>
            <div class="text-zinc-500 text-xs mt-1">Desbloqueadas</div>
        </div>

        <div class="bg-[#161616] border border-amber-800/30 rounded-2xl p-4 text-center">
            <div class="text-2xl mb-1">🥉</div>
            <div class="text-3xl font-bold text-amber-700 tabular-nums">{{ $stats['bronze'] }}</div>
            <div class="text-zinc-500 text-xs mt-1">Bronze</div>
        </div>

        <div class="bg-[#161616] border border-zinc-600/30 rounded-2xl p-4 text-center">
            <div class="text-2xl mb-1">🥈</div>
            <div class="text-3xl font-bold text-zinc-300 tabular-nums">{{ $stats['prata'] }}</div>
            <div class="text-zinc-500 text-xs mt-1">Prata</div>
        </div>

        <div class="bg-[#161616] border border-amber-700/30 rounded-2xl p-4 text-center">
            <div class="text-2xl mb-1">🥇</div>
            <div class="text-3xl font-bold text-amber-400 tabular-nums">{{ $stats['ouro'] }}</div>
            <div class="text-zinc-500 text-xs mt-1">Ouro</div>
        </div>

        <div class="col-span-2 sm:col-span-3 lg:col-span-1 bg-[#161616] border border-blue-800/30 rounded-2xl p-4 text-center">
            <div class="text-2xl mb-1">⚡</div>
            <div class="text-3xl font-bold text-blue-400 tabular-nums">{{ number_format($stats['xp']) }}</div>
            <div class="text-zinc-500 text-xs mt-1">XP de conquistas</div>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════════════════════
     3. LISTA DE CONQUISTAS
════════════════════════════════════════════════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">

    <div class="flex items-center gap-3 mb-2">
        <h2 class="text-zinc-400 text-sm font-medium uppercase tracking-widest">
            Conquistas pessoais
        </h2>
        <span class="text-zinc-700 text-xs">
            {{ $listaConquistas->where('desbloqueada', true)->count() }}
            /
            {{ $listaConquistas->count() }} desbloqueadas
        </span>
    </div>

    {{-- Separador --}}
    <div class="h-px bg-zinc-800 mb-0"></div>

    <div class="divide-y divide-zinc-800/70">

        @foreach ($listaConquistas as $item)
            @php
                $bloqueada  = ! $item['desbloqueada'];

                $nivelChave = match($item['tipo']) {
                    'progressiva' => $item['nivel_atual'] ?? 'bronze',
                    default       => $item['nivel'] ?? 'bronze',
                };

                $cor = $bloqueada ? [
                    'text'   => 'text-zinc-600',
                    'bg'     => 'bg-zinc-900/20',
                    'border' => 'border-zinc-800/40',
                    'glow'   => '',
                    'bar'    => 'bg-zinc-700',
                    'ring'   => '',
                ] : ($corNivel[$nivelChave] ?? $corNivel['bronze']);

                // Resolve o caminho do ícone a partir do config
                $iconePath = match($item['tipo']) {
                    'progressiva' => $item['nivel_atual']
                        ? ($item['nivel_atual_def']['icone'] ?? null)
                        : ($item['proximo_nivel_def']['icone'] ?? ($item['def']['niveis']['bronze']['icone'] ?? null)),
                    default => $item['def']['icone'] ?? null,
                };
                $iconeHtml = $iconePath
                    ? '<img src="' . asset($iconePath) . '" alt="' . e($item['titulo']) . '" class="w-full h-full object-contain p-2">'
                    : $iconeDefault;

                $xpConquista = match($item['tipo']) {
                    'progressiva' => $item['nivel_atual'] ? ($item['nivel_atual_def']['xp'] ?? 0) : 0,
                    default       => $item['def']['xp'] ?? 0,
                };
                $descricaoCard = match($item['tipo']) {
                    'progressiva' => $item['nivel_atual']
                        ? ($item['nivel_atual_def']['descricao'] ?? '')
                        : ($item['proximo_nivel_def']['descricao'] ?? ($item['def']['titulo'] ?? '')),
                    default => $item['def']['descricao'] ?? '',
                };
                $dataDesbloqueio = match($item['tipo']) {
                    'progressiva' => $item['ultima_data'] ?? null,
                    default       => isset($item['ganha']) ? ($item['ganha']?->criado_em ?? null) : null,
                };
                $glowColor = match($item['tipo'] === 'progressiva' ? ($item['proximo_nivel'] ?? $nivelChave) : $nivelChave) {
                    'ouro'  => 'rgba(251,191,36,0.55)',
                    'prata' => 'rgba(212,212,216,0.40)',
                    default => 'rgba(180,83,9,0.55)',
                };
            @endphp

            {{-- ── LINHA DE LISTA (estilo Steam) ── --}}
            <div class="group/row flex items-center gap-4 py-4 px-3
                        transition-colors hover:bg-zinc-800/30 rounded-sm">

                {{-- Ícone --}}
                <div class="w-14 h-14 sm:w-16 sm:h-16 shrink-0
                             {{ $bloqueada ? 'grayscale opacity-40' : '' }}">
                    {!! $iconeHtml !!}
                </div>

                {{-- Centro: título + descrição + barra (progressiva) --}}
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <p class="text-white text-sm font-semibold leading-tight">
                            {{ $item['titulo'] }}
                        </p>
                        {{-- Badge de nível --}}
                        @if (! $bloqueada)
                            @if ($item['tipo'] === 'progressiva' && $item['nivel_atual'])
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold
                                             rounded-full px-1.5 py-0.5 shrink-0
                                             {{ $cor['bg'] }} {{ $cor['text'] }} border {{ $cor['border'] }}">
                                    {{ $medalha[$item['nivel_atual']] }} {{ ucfirst($item['nivel_atual']) }}
                                </span>
                            @elseif ($item['tipo'] === 'unica')
                                <span class="inline-flex items-center gap-0.5 text-[10px] font-semibold
                                             rounded-full px-1.5 py-0.5 shrink-0
                                             {{ $cor['bg'] }} {{ $cor['text'] }} border {{ $cor['border'] }}">
                                    {{ $medalha[$nivelChave] ?? '🏅' }} {{ ucfirst($nivelChave) }}
                                </span>
                            @endif
                        @endif
                    </div>

                    <p class="text-zinc-500 text-xs leading-snug">{{ $descricaoCard }}</p>

                    {{-- Barra de progresso para conquistas progressivas --}}
                    @if ($item['tipo'] === 'progressiva' && ! $item['concluida'] && $item['meta_proxima'])
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700
                                            {{ $corNivel[$item['proximo_nivel'] ?? $nivelChave]['bar'] ?? 'bg-zinc-500' }}"
                                     style="width: {{ $item['percentual'] }}%;
                                            box-shadow: 0 0 6px {{ $item['percentual'] > 5 ? $glowColor : 'transparent' }}">
                                </div>
                            </div>
                            <span class="text-zinc-500 text-[10px] tabular-nums shrink-0">
                                {{ $item['atual'] }} / {{ $item['meta_proxima'] }}
                            </span>
                            {{-- Medalhas dim --}}
                            <div class="flex gap-0.5 shrink-0">
                                @foreach (['bronze', 'prata', 'ouro'] as $n)
                                    @php $ganhou = $item['ganhas']->where('nivel_conquista', $n)->isNotEmpty(); @endphp
                                    <span class="text-xs leading-none {{ $ganhou ? '' : 'opacity-20 grayscale' }}">
                                        {{ $medalha[$n] }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @elseif ($item['tipo'] === 'progressiva' && $item['concluida'])
                        <div class="mt-2 flex items-center gap-2">
                            <div class="flex-1 h-1.5 bg-amber-400 rounded-full
                                        shadow-[0_0_6px_rgba(251,191,36,0.5)]"></div>
                            <span class="text-amber-400 text-[10px] font-bold shrink-0">🥉🥈🥇 Completo!</span>
                        </div>
                    @endif
                </div>

                {{-- Direita: data de desbloqueio ou cadeado --}}
                <div class="shrink-0 text-right min-w-[120px] hidden sm:block">
                    @if ($bloqueada)
                        <svg class="w-4 h-4 text-zinc-700 ml-auto" fill="none" stroke="currentColor"
                             stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    @elseif ($dataDesbloqueio)
                        <p class="text-zinc-400 text-xs">Alcançada em</p>
                        <p class="text-zinc-300 text-xs font-medium mt-0.5">
                            {{ $dataDesbloqueio->format('d/m/Y') }}
                        </p>
                        @if ($xpConquista)
                            <p class="text-blue-400 text-[10px] mt-1">⚡ +{{ $xpConquista }} XP</p>
                        @endif
                    @else
                        <p class="text-zinc-600 text-xs">Em progresso</p>
                    @endif
                </div>

            </div>
        @endforeach

    </div>
</section>

@endsection
