<?php

namespace App\Http\Controllers\Livro;

use App\Http\Controllers\Controller;
use App\Models\ComentarioLivro;
use App\Models\Livro;
use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
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

    public function index(Request $request)
    {
        return view('index');
    }

    public function buscar(Request $request)
    {
        $query = trim($request->input('buscar', ''));

        if (empty($query)) {
            return redirect()->route('livros.index');
        }

        $pagina    = (int) $request->input('page', 1);
        $porPagina = 10;

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

        $notaUsuario     = null;
        $notasPorUsuario = collect();

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
        }

        return view('livro.show', [
            'livro'           => $livro,
            'estantes'        => $estantes,
            'comentarios'     => $comentarios,
            'notaUsuario'     => $notaUsuario,
            'notasPorUsuario' => $notasPorUsuario,
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
