<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use App\Models\ComentarioLivro;
use App\Models\Nota;
use App\Models\Postagem;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
class IndexController extends Controller
{
    /**
     * Landing page pública para visitantes não autenticados.
     * Usuários logados são redirecionados para a home (/home).
     */
    private function apiUrl(string $caminho = ''): string
    {
        return rtrim(config('services.google_books.url'), '/') . $caminho;
    }

    private function apiKey(): string
    {
        return config('services.google_books.key', '');
    }

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

    private function buscarCapasHero(): array
    {
        return Cache::remember('hero_capas_v4', now()->addHours(6), function () {
            $categorias = [
                'subject:fiction',
                'subject:romance',
                'subject:fantasy',
                'subject:science fiction',
                'subject:mystery',
                'subject:thriller',
                'subject:drama',
                'subject:adventure',
            ];

            $apiUrl = $this->apiUrl('/volumes');
            $apiKey = $this->apiKey();

            try {
                $respostas = Http::pool(fn(Pool $pool) => array_map(
                    fn($categoria) => $pool->timeout(6)->get($apiUrl, [
                        'q' => $categoria,
                        'maxResults' => 12,
                        'orderBy' => 'relevance',
                        'printType' => 'books',
                        'langRestrict' => 'en',
                        'key' => $apiKey,
                    ]),
                    $categorias
                ));

                return collect($respostas)
                    ->flatMap(function ($resposta) {
                        if (!($resposta instanceof Response) || $resposta->failed()) {
                            return [];
                        }

                        return collect($resposta->json()['items'] ?? [])
                            ->filter(fn($item) => $this->capasValidas($item))
                            ->map(fn($item) => str_replace(
                                'http://',
                                'https://',
                                $item['volumeInfo']['imageLinks']['thumbnail']
                                ?? $item['volumeInfo']['imageLinks']['smallThumbnail']
                                ?? null
                            ))
                            ->filter();
                    })
                    ->unique()       // Remove capas duplicadas entre gêneros.
                    ->shuffle()      // Mistura os gêneros no mosaico.
                    ->values()
                    ->all();

            } catch (\Throwable) {
                return [];
            }
        });
    }

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

    public function index(Request $request)
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        // 5 postagens em destaque — ordenadas pela quantidade de curtidas (desc),
        // depois por contagem de comentários, depois por data de criação.
        $postagens = Postagem::with(['usuario', 'livro'])
            ->withCount(['comentarios', 'curtidoresPor as likes_count'])
            ->orderByDesc('likes_count')
            ->orderByDesc('comentarios_count')
            ->orderByDesc('criado_em')
            ->take(5)
            ->get();

        $capas      = $this->buscarCapasHero();
        $interacoes = $this->buscarInteracoesRecentes(4);

        return view('index', compact('capas', 'interacoes', 'postagens'));
    }


}
