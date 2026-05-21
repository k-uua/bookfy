{{-- ── Notificações de conquista desbloqueada ─────────────────────────────── --}}
@if (session('conquistas_desbloqueadas'))
    @php
        $medalhaEmoji = ['bronze' => '🥉', 'prata' => '🥈', 'ouro' => '🥇'];
        $corBorder    = ['bronze' => 'border-amber-800/60', 'prata' => 'border-zinc-500/60', 'ouro' => 'border-amber-600/60'];
        $corBg        = ['bronze' => 'bg-amber-950/70',     'prata' => 'bg-zinc-800/70',     'ouro' => 'bg-amber-900/70'];
        $corTitulo    = ['bronze' => 'text-amber-600',      'prata' => 'text-zinc-200',       'ouro' => 'text-amber-400'];
    @endphp
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full space-y-2">
        @foreach (session('conquistas_desbloqueadas') as $conquista)
            @php $nivel = $conquista['nivel'] ?? 'bronze'; @endphp
            <div class="flex items-start justify-between gap-4 rounded-xl border
                        {{ $corBorder[$nivel] ?? 'border-zinc-700' }}
                        {{ $corBg[$nivel] ?? 'bg-zinc-900' }}
                        px-4 py-3">
                <div class="flex items-start gap-3 min-w-0">
                    {{-- Medalha --}}
                    <span class="text-2xl leading-none shrink-0 mt-0.5">
                        {{ $medalhaEmoji[$nivel] ?? '🏅' }}
                    </span>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold text-zinc-400 uppercase tracking-wider mb-0.5">
                            🏆 Conquista desbloqueada!
                        </p>
                        <p class="text-sm font-bold {{ $corTitulo[$nivel] ?? 'text-white' }} leading-tight">
                            {{ $conquista['titulo'] }}
                            <span class="font-normal text-zinc-400 text-xs ml-1">({{ ucfirst($nivel) }})</span>
                        </p>
                        @if (! empty($conquista['mensagem_desbloqueio']))
                            <p class="text-zinc-300 text-xs mt-1 leading-relaxed">
                                {{ $conquista['mensagem_desbloqueio'] }}
                            </p>
                        @elseif (! empty($conquista['descricao']))
                            <p class="text-zinc-300 text-xs mt-1 leading-relaxed">
                                {{ $conquista['descricao'] }}
                            </p>
                        @endif
                    </div>
                </div>
                <div class="flex flex-col items-end gap-1.5 shrink-0">
                    @if ($conquista['xp'] ?? 0)
                        <span class="text-blue-400 text-xs font-bold whitespace-nowrap">
                            ⚡ +{{ $conquista['xp'] }} XP
                        </span>
                    @endif
                    <button type="button" onclick="this.closest('[class*=rounded-xl]').remove()"
                            class="text-zinc-500 hover:text-white transition-colors text-lg leading-none">
                        &times;
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@endif

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
