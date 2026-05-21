<?php

namespace App\Http\Controllers\Conquista;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ConquistaController extends Controller
{
    // ── Ranks por nível ──────────────────────────────────────────────────────

    private static array $ranks = [
        20 => 'Lenda Literária',
        15 => 'Mestre das Páginas',
        10 => 'Curador Literário',
        5  => 'Crítico em Formação',
        2  => 'Explorador Literário',
        0  => 'Leitor Iniciante',
    ];

    private function getRank(int $nivel): string
    {
        foreach (self::$ranks as $min => $titulo) {
            if ($nivel >= $min) {
                return $titulo;
            }
        }
        return 'Leitor Iniciante';
    }

    // ── Métricas de progresso ────────────────────────────────────────────────

    /**
     * Retorna map: codigo_conquista → valor atual (int).
     * Executa cada closure lazy — só calcula quando necessário.
     */
    private function calcularMetricas($usuario): array
    {
        $totalLivrosEstante = null;
        $favoritosCount     = null;

        return [
            'colecionador_literario' => function () use ($usuario, &$totalLivrosEstante) {
                if ($totalLivrosEstante === null) {
                    $totalLivrosEstante = $usuario->estantes()
                        ->withCount('livros')
                        ->get()
                        ->sum('livros_count');
                }
                return $totalLivrosEstante;
            },
            'primeira_estante' => function () use ($usuario) {
                return min(1, $usuario->estantes()->count());
            },
            'primeiro_livro_favoritado' => function () use ($usuario, &$favoritosCount) {
                if ($favoritosCount === null) {
                    $favoritosCount = min(1,
                        $usuario->estantes()
                            ->where('nome', 'Favoritos')
                            ->withCount('livros')
                            ->first()?->livros_count ?? 0
                    );
                }
                return $favoritosCount;
            },
        ];
    }

    private function resolverMetrica(array $metricas, string $codigo): int
    {
        if (! isset($metricas[$codigo])) {
            return 0;
        }
        return (int) ($metricas[$codigo])();
    }

    // ── Montagem da lista unificada ──────────────────────────────────────────

    private function montarLista($conquistasGanhas, array $metricas): \Illuminate\Support\Collection
    {
        $niveisOrdem = ['bronze', 'prata', 'ouro'];

        return collect(config('conquistas', []))->map(function ($def, $codigo) use ($conquistasGanhas, $metricas, $niveisOrdem) {
            $ganhas       = $conquistasGanhas->get($codigo, collect());
            $ehProgressiva = ! empty($def['possui_niveis']);

            if ($ehProgressiva) {
                // Nível mais alto já ganho
                $nivelAtual = null;
                foreach (array_reverse($niveisOrdem) as $n) {
                    if ($ganhas->where('nivel_conquista', $n)->isNotEmpty()) {
                        $nivelAtual = $n;
                        break;
                    }
                }

                // Próximo nível a ganhar
                $idxAtual    = $nivelAtual ? array_search($nivelAtual, $niveisOrdem) : -1;
                $proximoNivel = $niveisOrdem[$idxAtual + 1] ?? null;

                $atual       = $this->resolverMetrica($metricas, $codigo);
                $metaProxima = $proximoNivel ? (int) ($def['niveis'][$proximoNivel]['meta'] ?? 0) : 0;
                $percentual  = $metaProxima > 0 ? min(100, (int) round($atual / $metaProxima * 100)) : 100;

                return [
                    'codigo'           => $codigo,
                    'tipo'             => 'progressiva',
                    'titulo'           => $def['titulo'] ?? $codigo,
                    'def'              => $def,
                    'ganhas'           => $ganhas,
                    'nivel_atual'      => $nivelAtual,
                    'nivel_atual_def'  => $nivelAtual ? ($def['niveis'][$nivelAtual] ?? []) : [],
                    'proximo_nivel'    => $proximoNivel,
                    'proximo_nivel_def'=> $proximoNivel ? ($def['niveis'][$proximoNivel] ?? []) : [],
                    'atual'            => $atual,
                    'meta_proxima'     => $metaProxima,
                    'percentual'       => $percentual,
                    'desbloqueada'     => $nivelAtual !== null,
                    'concluida'        => $nivelAtual === 'ouro',
                    'ultima_data'      => $ganhas->sortByDesc('criado_em')->first()?->criado_em,
                ];
            }

            $ganha = $ganhas->first();

            return [
                'codigo'       => $codigo,
                'tipo'         => 'unica',
                'titulo'       => $def['titulo'] ?? $codigo,
                'def'          => $def,
                'ganha'        => $ganha,
                'nivel'        => $def['nivel'] ?? 'bronze',
                'desbloqueada' => $ganha !== null,
            ];
        })->sortByDesc('desbloqueada')->values();
    }

    // ── Action principal ─────────────────────────────────────────────────────

    public function index()
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        // Todas as conquistas ganhas, agrupadas por codigo
        $conquistasGanhas = $usuario->conquistas()
            ->orderBy('criado_em', 'desc')
            ->get()
            ->groupBy('codigo_conquista');

        // Closures de métricas (lazy)
        $metricas = $this->calcularMetricas($usuario);

        // Lista unificada para a view
        $listaConquistas = $this->montarLista($conquistasGanhas, $metricas);

        // Estatísticas gerais
        $todasGanhas = $conquistasGanhas->flatten();
        $xpTotal     = $todasGanhas->sum('xp');

        $stats = [
            'total'  => $todasGanhas->count(),
            'bronze' => $todasGanhas->where('nivel_conquista', 'bronze')->count(),
            'prata'  => $todasGanhas->where('nivel_conquista', 'prata')->count(),
            'ouro'   => $todasGanhas->where('nivel_conquista', 'ouro')->count(),
            'xp'     => $xpTotal,
        ];

        // XP / nível / rank
        $xp             = $usuario->xp ?? 0;
        $nivel          = $usuario->nivel;
        $rank           = $this->getRank($nivel);
        $xpNivelAtual   = $nivel ** 2 * 10;
        $xpProximoNivel = ($nivel + 1) ** 2 * 10;
        $xpNoNivel      = $xp - $xpNivelAtual;
        $xpParaSubir    = $xpProximoNivel - $xpNivelAtual;
        $progressoXp    = $xpParaSubir > 0 ? min(100, (int) ($xpNoNivel / $xpParaSubir * 100)) : 100;

        return view('conquistas.index', compact(
            'usuario',
            'listaConquistas',
            'stats',
            'nivel',
            'rank',
            'xp',
            'xpNoNivel',
            'xpParaSubir',
            'progressoXp'
        ));
    }
}
