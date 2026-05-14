<?php

namespace App\Http\Controllers\Livro;

use App\Http\Controllers\Controller;
use App\Models\ComentarioLivro;
use App\Models\Livro;
use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
class LivroController extends Controller
{
    private function apiUrl(string $caminho = ''): string
    {
        return rtrim(config('services.google_books.url'), '/') . $caminho;
    }

    private function apiKey(): string
    {
        return config('services.google_books.key', '');
    }

    /**
     * Home autenticada — exibe recomendações personalizadas, livros mais bem
     * avaliados, interações recentes e o painel de progresso de conquistas.
     */
    public function home()
    {
        $usuario = Auth::user();

        return view('home', [
            'recomendados'      => $this->buscarRecomendados(),
            'melhoresAvaliados' => $this->buscarMelhoresAvaliados(),
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
     * Livros sugeridos para o usuário — pega títulos populares mesclando
     * múltiplos gêneros para dar variedade na primeira aparição.
     */
    private function buscarRecomendados(): array
    {
        return Cache::remember('home_recomendados_v1', now()->addHours(2), function () {
            $generos = ['fiction', 'fantasy', 'romance'];

            try {
                $respostas = Http::pool(fn (Pool $pool) => array_map(
                    fn ($g) => $pool->timeout(5)->get($this->apiUrl('/volumes'), [
                        'q'            => "subject:{$g}",
                        'maxResults'   => 6,
                        'orderBy'      => 'relevance',
                        'printType'    => 'books',
                        'langRestrict' => 'en',
                        'key'          => $this->apiKey(),
                    ]),
                    $generos
                ));

                return collect($respostas)
                    ->flatMap(fn ($r) => ($r instanceof Response && $r->successful())
                        ? ($r->json()['items'] ?? []) : [])
                    ->filter(fn ($item) => $this->capasValidas($item))
                    ->map(fn ($item) => $this->normalizarLivro($item))
                    ->unique('id')
                    ->shuffle()
                    ->take(5)
                    ->values()
                    ->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    /**
     * Livros com nota média alta (>= 4.0) entre títulos populares.
     */
    private function buscarMelhoresAvaliados(): array
    {
        return Cache::remember('home_top_rated_v1', now()->addHours(2), function () {
            try {
                $resposta = Http::timeout(6)->get($this->apiUrl('/volumes'), [
                    'q'            => 'subject:fiction',
                    'maxResults'   => 30,
                    'orderBy'      => 'relevance',
                    'printType'    => 'books',
                    'langRestrict' => 'en',
                    'key'          => $this->apiKey(),
                ]);

                if ($resposta->failed()) {
                    return [];
                }

                return collect($resposta->json()['items'] ?? [])
                    ->filter(fn ($item) => $this->capasValidas($item)
                        && (($item['volumeInfo']['averageRating'] ?? 0) >= 4))
                    ->map(fn ($item) => $this->normalizarLivro($item))
                    ->sortByDesc('rating')
                    ->take(5)
                    ->values()
                    ->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    /**
     * Normaliza um item da API Google Books para um array enxuto consumido
     * pela view (componente <x-book-card />).
     */
    private function normalizarLivro(array $item): array
    {
        $info = $item['volumeInfo'] ?? [];

        return [
            'id'         => $item['id'] ?? null,
            'titulo'     => $info['title'] ?? 'Sem título',
            'autores'    => $info['authors'] ?? [],
            'capa'       => str_replace('http://', 'https://',
                $info['imageLinks']['thumbnail']
                ?? $info['imageLinks']['smallThumbnail']
                ?? ''),
            'rating'     => round((float) ($info['averageRating'] ?? 0), 1),
            'lancamento' => $info['publishedDate'] ?? null,
        ];
    }

    /**
     * Calcula o progresso do usuário em direção às conquistas.
     * Retorna um array de conquistas com atual/meta/percentual.
     */
    private function calcularProgresso($usuario): array
    {
        $conquistas = [
            [
                'titulo'    => 'Crítico iniciante',
                'descricao' => 'Avalie 10 livros',
                'atual'     => $usuario->notas()->count(),
                'meta'      => 10,
                'icone'     => 'star',
            ],
            [
                'titulo'    => 'Participante ativo',
                'descricao' => 'Poste 5 comentários',
                'atual'     => $usuario->comentariosLivro()->count(),
                'meta'      => 5,
                'icone'     => 'chat',
            ],
            [
                'titulo'    => 'Colecionador',
                'descricao' => 'Crie 3 estantes',
                'atual'     => $usuario->estantes()->count(),
                'meta'      => 3,
                'icone'     => 'shelf',
            ],
        ];

        return array_map(function ($c) {
            $c['percentual'] = $c['meta'] > 0
                ? min(100, (int) round($c['atual'] / $c['meta'] * 100))
                : 0;
            return $c;
        }, $conquistas);
    }


    /**
     * Valida se um item da API é adequado para o mosaico:
     *  — Deve ter imagem de capa.
     *  — Deve ter ISBN (livros pré-copyright muitas vezes não têm ISBN registrado,
     *    o que ajuda a filtrar domínio público obscuro).
     *  — Se a data de publicação estiver presente, deve ser >= 1970.
     */
    private function capasValidas(array $item): bool
    {
        $info = $item['volumeInfo'] ?? [];

        if (empty($info['imageLinks'])) {
            return false;
        }

        if (empty($info['industryIdentifiers'])) {
            return false;
        }

        $dataPublicacao = $info['publishedDate'] ?? '';
        if ($dataPublicacao !== '') {
            $ano = (int) substr($dataPublicacao, 0, 4);
            if ($ano > 0 && $ano < 1970) {
                return false;
            }
        }

        return true;
    }

    public function buscar(Request $request)
    {
        $query = trim($request->input('buscar', ''));

        if (empty($query)) {
            return redirect()->route('livros.index');
        }

        $pagina    = (int) $request->input('page', 1);
        $porPagina = 20;

        // Buscamos o dobro por página para ter candidatos suficientes ao reordenar.
        $buscarMax  = $porPagina * 2;
        $startIndex = ($pagina - 1) * $buscarMax;

        $response = Http::get($this->apiUrl('/volumes'), [
            'q'          => $query,
            'startIndex' => $startIndex,
            'maxResults' => $buscarMax,
            'orderBy'    => 'relevance',
            'printType'  => 'books',
            'key'        => $this->apiKey(),
        ]);

        if ($response->failed()) {
            return back()->withErrors(['erro' => 'Falha na busca. Tente novamente.']);
        }

        $dados = $response->json();
        $itens = $dados['items'] ?? [];
        $total = min($dados['totalItems'] ?? 0, 1000);

        // ── Pontuação local de relevância ─────────────────────────────────────
        $queryNorm = mb_strtolower($query);

        $ordenados = collect($itens)
            ->map(function (array $item) use ($queryNorm): array {
                $info   = $item['volumeInfo'] ?? [];
                $titulo = mb_strtolower($info['title'] ?? '');
                $score  = 0;

                // Correspondência no título (peso alto)
                if ($titulo === $queryNorm) {
                    $score += 80;
                } elseif (str_starts_with($titulo, $queryNorm)) {
                    $score += 70;
                } elseif (str_contains($titulo, $queryNorm)) {
                    $score += 45;
                }

                // Sinais de qualidade do item
                if (!empty($info['imageLinks']))  $score += 100; // tem capa
                if (!empty($info['description'])) $score += 5;  // tem sinopse

                $qtdAvaliacoes = (int) ($info['ratingsCount']  ?? 0);
                $mediaNotas    = (float) ($info['averageRating'] ?? 0);

                if ($qtdAvaliacoes > 0) {
                    // Escala logarítmica: 10 avaliações ≈ +8 pts, 1000 ≈ +24 pts
                    $score += (int) (log10($qtdAvaliacoes + 1) * 8);
                    // Nota média: máximo +10 pontos (para 5 estrelas)
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
        $response = Http::get($this->apiUrl("/volumes/{$id}"), [
            'key' => $this->apiKey(),
        ]);

        if ($response->failed()) {
            abort(404);
        }

        $livro = $response->json();

        $idioma    = $livro['volumeInfo']['language'] ?? 'en';
        $descricao = $livro['volumeInfo']['description'] ?? null;

        if ($descricao && ! str_starts_with($idioma, 'pt')) {
            $livro['volumeInfo']['description'] = $this->traduzir($descricao);
        }

        $estantes = Auth::check() ? Auth::user()->estantes : collect();

        $livroLocal  = Livro::where('google_books_id', $id)->first();
        $comentarios = $livroLocal ? ComentarioLivro::doLivro($livroLocal->id) : collect();

        $notaUsuario          = null;
        $notasPorUsuario      = collect();
        $notaBookfy           = null;
        $totalAvaliacoesBookfy = 0;

        if ($livroLocal) {
            // Nota do usuário autenticado para exibir no widget de estrelas.
            if (Auth::check()) {
                $notaUsuario = Nota::where('id_usuario', Auth::id())
                    ->where('id_livro', $livroLocal->id)
                    ->first();
            }

            // Notas de todos os comentadores em uma única query (evita N+1).
            $notasPorUsuario = Nota::where('id_livro', $livroLocal->id)
                ->get()
                ->keyBy('id_usuario');

            // Média Bookfy — derivada do conjunto já carregado (sem query extra).
            if ($notasPorUsuario->isNotEmpty()) {
                $notaBookfy            = round($notasPorUsuario->avg('nota'), 1);
                $totalAvaliacoesBookfy = $notasPorUsuario->count();
            }
        }

        return view('livro.show', [
            'livro'                 => $livro,
            'estantes'              => $estantes,
            'comentarios'           => $comentarios,
            'notaUsuario'           => $notaUsuario,
            'notasPorUsuario'       => $notasPorUsuario,
            'notaBookfy'            => $notaBookfy,
            'totalAvaliacoesBookfy' => $totalAvaliacoesBookfy,
        ]);
    }

    public function categorias(Request $request)
    {
        $query     = $request->input('categoria');
        $pagina    = (int) $request->input('page', 1);
        $porPagina = 10;
        $startIndex = ($pagina - 1) * $porPagina;

        $response = Http::get($this->apiUrl('/volumes'), [
            'q'          => 'subject:' . $query,
            'startIndex' => $startIndex,
            'maxResults' => $porPagina,
            'key'        => $this->apiKey(),
        ]);

        if ($response->failed()) {
            return back()->withErrors(['erro' => 'Não foi possível carregar a categoria. Tente novamente.']);
        }

        $dados = $response->json();

        $livros = $dados['items'] ?? [];
        $total  = min($dados['totalItems'] ?? 0, 1000);

        $paginacao = new LengthAwarePaginator(
            $livros,
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
}
