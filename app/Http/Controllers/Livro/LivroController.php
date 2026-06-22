<?php

namespace App\Http\Controllers\Livro;

use App\Http\Controllers\Controller;
use App\Models\ComentarioLivro;
use App\Models\Livro;
use App\Models\Nota;
use App\Services\GoogleBooksService;
use App\Services\RecomendacaoService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LivroController extends Controller
{
    public function __construct(
        private GoogleBooksService $googleBooks,
        private RecomendacaoService $recomendacoes,
    ) {}

    /**
     * Home autenticada — exibe recomendações personalizadas, livros mais bem
     * avaliados, interações recentes e o painel de progresso de conquistas.
     */
    public function home()
    {
        $usuario = Auth::user();

        return view('home', [
            'recomendados'      => $this->recomendacoes->paraUsuario($usuario->id),
            'melhoresAvaliados' => $this->googleBooks->melhoresAvaliados(),
            'interacoes'        => $this->buscarInteracoesRecentes(3),
            'progresso'         => $this->calcularProgresso($usuario),
        ]);
    }

    /**
     * Carrega os comentários raiz mais recentes com usuário + livro + nota
     * do comentador (esta última via uma única query — sem N+1).
     */
    private function buscarInteracoesRecentes(int $limite = 4): \Illuminate\Support\Collection
    {
        $interacoes = ComentarioLivro::with(['usuario', 'livro'])
            ->whereHas('livro')
            ->whereHas('usuario')
            ->whereNull('id_comentario_pai')
            ->latest('criado_em')
            ->take($limite)
            ->get();

        if ($interacoes->isEmpty()) {
            return $interacoes;
        }

        $notasMap = Nota::whereIn('id_usuario', $interacoes->pluck('id_usuario'))
            ->whereIn('id_livro', $interacoes->pluck('id_livro'))
            ->get()
            ->keyBy(fn($n) => $n->id_usuario . '_' . $n->id_livro);

        return $interacoes->map(function ($c) use ($notasMap) {
            $c->notaUsuario = $notasMap->get($c->id_usuario . '_' . $c->id_livro);
            return $c;
        });
    }

    /**
     * Calcula o progresso do usuário em direção às conquistas definidas em
     * config/conquistas.php. Conquistas com níveis mostram o próximo nível
     * pendente; conquistas únicas mostram 0% ou 100%.
     */
    private function calcularProgresso($usuario): array
    {
        $metricas = [
            'colecionador_literario'    => fn () => $usuario->estantes()
                ->withCount('livros')
                ->get()
                ->sum('livros_count'),
            'primeira_estante'          => fn () => min(1, $usuario->estantes()->count()),
            'primeiro_livro_favoritado' => fn () => min(1,
                $usuario->estantes()
                    ->where('nome', 'Favoritos')
                    ->withCount('livros')
                    ->first()?->livros_count ?? 0
            ),
        ];

        $icones = ['progresso' => 'shelf', 'social' => 'chat', 'leitura' => 'star'];

        $resultado = [];

        foreach (config('conquistas', []) as $codigo => $def) {
            $atual = isset($metricas[$codigo]) ? ($metricas[$codigo])() : 0;

            if (! empty($def['possui_niveis'])) {
                foreach (['bronze', 'prata', 'ouro'] as $nivel) {
                    $nivelDef = $def['niveis'][$nivel] ?? null;
                    if (! $nivelDef) {
                        continue;
                    }
                    $meta = (int) ($nivelDef['meta'] ?? 1);
                    if ($atual < $meta) {
                        $resultado[] = [
                            'titulo'     => ($def['titulo'] ?? $codigo) . ' — ' . ucfirst($nivel),
                            'descricao'  => $nivelDef['descricao'] ?? '',
                            'atual'      => $atual,
                            'meta'       => $meta,
                            'icone'      => $icones[$def['categoria'] ?? ''] ?? 'star',
                            'percentual' => min(100, (int) round($atual / $meta * 100)),
                        ];
                        break;
                    }
                }
            } else {
                $resultado[] = [
                    'titulo'     => $def['titulo'] ?? $codigo,
                    'descricao'  => $def['descricao'] ?? '',
                    'atual'      => $atual,
                    'meta'       => 1,
                    'icone'      => $icones[$def['categoria'] ?? ''] ?? 'star',
                    'percentual' => $atual >= 1 ? 100 : 0,
                ];
            }
        }

        return $resultado;
    }

    public function buscar(Request $request)
    {
        $query = trim($request->input('buscar', ''));

        if (empty($query)) {
            return redirect()->route('livros.index');
        }

        $pagina    = (int) $request->input('page', 1);
        $porPagina = 20;
        $buscarMax = $porPagina * 2;
        $startIndex = ($pagina - 1) * $buscarMax;

        $dados = $this->googleBooks->buscar($query, $startIndex, $buscarMax);
        $total = min($dados['totalItems'], 1000);

        // ── Pontuação local de relevância ─────────────────────────────────────
        $queryNorm = mb_strtolower($query);

        $ordenados = collect($dados['items'])
            ->map(function (array $item) use ($queryNorm): array {
                $info   = $item['volumeInfo'] ?? [];
                $titulo = mb_strtolower($info['title'] ?? '');
                $score  = 0;

                if ($titulo === $queryNorm) {
                    $score += 80;
                } elseif (str_starts_with($titulo, $queryNorm)) {
                    $score += 70;
                } elseif (str_contains($titulo, $queryNorm)) {
                    $score += 45;
                }

                if (!empty($info['imageLinks']))  $score += 100;
                if (!empty($info['description'])) $score += 5;

                $qtdAvaliacoes = (int) ($info['ratingsCount']   ?? 0);
                $mediaNotas    = (float) ($info['averageRating'] ?? 0);

                if ($qtdAvaliacoes > 0) {
                    $score += (int) (log10($qtdAvaliacoes + 1) * 8);
                    $score += (int) ($mediaNotas * 2);
                }

                $item['_score'] = $score;
                return $item;
            })
            ->sortByDesc('_score')
            ->values()
            ->take($porPagina);

        $paginacao = new LengthAwarePaginator(
            $ordenados,
            $total,
            $porPagina,
            $pagina,
            [
                'path'  => url()->current(),
                'query' => $request->query(),
            ]
        );

        return view('components.livros', ['livros' => $paginacao]);
    }

    public function show($id)
    {
        $livro = $this->googleBooks->volume($id);

        if (! $livro) {
            abort(404);
        }

        $idioma    = $livro['volumeInfo']['language'] ?? 'en';
        $descricao = $livro['volumeInfo']['description'] ?? null;
        $isbn      = $livro['volumeInfo']['industryIdentifiers'][0]['identifier'] ?? null;

        if ($descricao && ! str_starts_with($idioma, 'pt')) {
            $livro['volumeInfo']['description'] = $this->traduzir($descricao);
        }

        $estantes   = Auth::check() ? Auth::user()->estantes : collect();
        $isFavorito = false;

        $livroLocal  = Livro::where('google_books_id', $id)->first();
        $comentarios = $livroLocal ? ComentarioLivro::doLivro($livroLocal->id) : collect();

        $notaUsuario           = null;
        $notasPorUsuario       = collect();
        $notaBookfy            = null;
        $totalAvaliacoesBookfy = 0;

        if ($livroLocal) {
            if (Auth::check()) {
                $notaUsuario = Nota::where('id_usuario', Auth::id())
                    ->where('id_livro', $livroLocal->id)
                    ->first();
            }

            $notasPorUsuario = Nota::where('id_livro', $livroLocal->id)
                ->get()
                ->keyBy('id_usuario');

            if ($notasPorUsuario->isNotEmpty()) {
                $notaBookfy            = round($notasPorUsuario->avg('nota'), 1);
                $totalAvaliacoesBookfy = $notasPorUsuario->count();
            }

            if (Auth::check()) {
                $estanteFavoritos = Auth::user()
                    ->estantes()
                    ->where('nome', 'Favoritos')
                    ->first();

                if ($estanteFavoritos) {
                    $isFavorito = $estanteFavoritos->livros()
                        ->where('livro.id', $livroLocal->id)
                        ->exists();
                }
            }
        }

        return view('livro.show', [
            'livro'                 => $livro,
            'isbn'                  => $isbn,
            'estantes'              => $estantes,
            'isFavorito'            => $isFavorito,
            'comentarios'           => $comentarios,
            'notaUsuario'           => $notaUsuario,
            'notasPorUsuario'       => $notasPorUsuario,
            'notaBookfy'            => $notaBookfy,
            'totalAvaliacoesBookfy' => $totalAvaliacoesBookfy,
        ]);
    }

    public function categorias(Request $request)
    {
        $query      = $request->input('categoria');
        $pagina     = (int) $request->input('page', 1);
        $porPagina  = 10;
        $startIndex = ($pagina - 1) * $porPagina;

        $dados = $this->googleBooks->porCategoria($query, $startIndex, $porPagina);
        $total = min($dados['totalItems'], 1000);

        $paginacao = new LengthAwarePaginator(
            $dados['items'],
            $total,
            $porPagina,
            $pagina,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('components.livros', ['livros' => $paginacao]);
    }

    private function traduzir(string $texto): string
    {
        try {
            $response = Http::timeout(5)->get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl'     => 'auto',
                'tl'     => 'pt-BR',
                'dt'     => 't',
                'q'      => $texto,
            ]);

            if ($response->failed()) {
                return $texto;
            }

            $partes    = $response->json();
            $traduzido = collect($partes[0] ?? [])->pluck(0)->implode('');

            return $traduzido ?: $texto;
        } catch (\Throwable) {
            return $texto;
        }
    }

    public function linkCompra(string $ISBN)
    {
        return redirect("https://www.amazon.com.br/s?k={$ISBN}&i=stripbooks");
    }
}
