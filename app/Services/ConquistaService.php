<?php

namespace App\Services;

use App\Models\Usuario;
use App\Models\UsuarioConquista;

class ConquistaService
{
    // ── Métricas ─────────────────────────────────────────────────────────────

    /**
     * Calcula o valor atual do indicador de progresso para um dado código de
     * conquista. Adicione novos cases conforme novas conquistas forem criadas
     * em config/conquistas.php.
     */
    private function metrica(Usuario $usuario, string $codigo): int
    {
        return match ($codigo) {
            // Total de livros somados em todas as estantes do usuário
            'colecionador_literario' => (int) $usuario
                ->estantes()
                ->withCount('livros')
                ->get()
                ->sum('livros_count'),

            // 1 se a estante "Favoritos" tiver pelo menos um livro
            'primeiro_livro_favoritado' => min(1,
                $usuario->estantes()
                    ->where('nome', 'Favoritos')
                    ->withCount('livros')
                    ->first()?->livros_count ?? 0
            ),

            default => 0,
        };
    }

    // ── Verificação e registro ────────────────────────────────────────────────

    /**
     * Verifica os códigos de conquista informados para o usuário:
     *  — calcula a métrica atual
     *  — registra no banco os níveis recém-atingidos (evita duplicatas)
     *  — incrementa o XP do usuário pelos prêmios ganhos
     *
     * Retorna um array com as conquistas recém-desbloqueadas, incluindo todos
     * os campos de config (descricao, mensagem_desbloqueio, icone, xp).
     *
     * @param  string[]  $codigos
     * @return array<int, array{codigo: string, titulo: string, nivel: string,
     *                          descricao: string, mensagem_desbloqueio: string,
     *                          icone: string, xp: int}>
     */
    public function verificar(Usuario $usuario, array $codigos): array
    {
        $novas   = [];
        $xpTotal = 0;

        foreach ($codigos as $codigo) {
            $def = config("conquistas.{$codigo}");
            if (! $def) {
                continue;
            }

            $metrica = $this->metrica($usuario, $codigo);

            if (! empty($def['possui_niveis'])) {
                // ── Conquista progressiva: verifica cada nível ───────────────
                foreach (['bronze', 'prata', 'ouro'] as $nivel) {
                    $nivelDef = $def['niveis'][$nivel] ?? null;
                    if (! $nivelDef) {
                        continue;
                    }

                    // Usuário ainda não atingiu a meta deste nível?
                    if ($metrica < (int) ($nivelDef['meta'] ?? PHP_INT_MAX)) {
                        continue;
                    }

                    // Já possui este nível registrado?
                    $existe = UsuarioConquista::where('id_usuario', $usuario->id)
                        ->where('codigo_conquista', $codigo)
                        ->where('nivel_conquista', $nivel)
                        ->exists();

                    if ($existe) {
                        continue;
                    }

                    UsuarioConquista::create([
                        'id_usuario'      => $usuario->id,
                        'codigo_conquista' => $codigo,
                        'nivel_conquista'  => $nivel,
                    ]);

                    $xp       = (int) ($nivelDef['xp'] ?? 0);
                    $xpTotal += $xp;

                    $novas[] = [
                        'codigo'               => $codigo,
                        'titulo'               => $def['titulo'] ?? $codigo,
                        'nivel'                => $nivel,
                        'descricao'            => $nivelDef['descricao'] ?? '',
                        'mensagem_desbloqueio' => $nivelDef['mensagem_desbloqueio'] ?? '',
                        'icone'                => $nivelDef['icone'] ?? ($def['icone'] ?? ''),
                        'xp'                   => $xp,
                    ];
                }
            } else {
                // ── Conquista única ──────────────────────────────────────────
                if ($metrica < 1) {
                    continue;
                }

                $existe = UsuarioConquista::where('id_usuario', $usuario->id)
                    ->where('codigo_conquista', $codigo)
                    ->exists();

                if ($existe) {
                    continue;
                }

                $nivel = $def['nivel'] ?? 'bronze';

                UsuarioConquista::create([
                    'id_usuario'      => $usuario->id,
                    'codigo_conquista' => $codigo,
                    'nivel_conquista'  => $nivel,
                ]);

                $xp       = (int) ($def['xp'] ?? 0);
                $xpTotal += $xp;

                $novas[] = [
                    'codigo'               => $codigo,
                    'titulo'               => $def['titulo'] ?? $codigo,
                    'nivel'                => $nivel,
                    'descricao'            => $def['descricao'] ?? '',
                    'mensagem_desbloqueio' => $def['mensagem_desbloqueio'] ?? '',
                    'icone'                => $def['icone'] ?? '',
                    'xp'                   => $xp,
                ];
            }
        }

        if ($xpTotal > 0) {
            $usuario->increment('xp', $xpTotal);
        }

        return $novas;
    }

    // ── Helper: salva na sessão e encadeia o redirect ─────────────────────────

    /**
     * Persiste as conquistas recém-desbloqueadas na sessão para que o
     * componente de flash as exiba na próxima request.
     */
    public function flashNovas(array $novas): void
    {
        if (! empty($novas)) {
            session()->flash('conquistas_desbloqueadas', $novas);
        }
    }
}
