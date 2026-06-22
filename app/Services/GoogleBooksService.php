<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class GoogleBooksService
{
    private function baseUrl(): string
    {
        return rtrim(config('services.google_books.url'), '/');
    }

    private function key(): string
    {
        return config('services.google_books.key', '');
    }

    /**
     * Detalhes completos de um volume — usado na página do livro.
     */
    public function volume(string $id): ?array
    {
        $resposta = Http::get($this->baseUrl() . "/volumes/{$id}", [
            'key' => $this->key(),
        ]);

        return $resposta->successful() ? $resposta->json() : null;
    }

    /**
     * Busca livre na API do Google Books — retorna os itens crus e o total,
     * deixando o ranqueamento/paginação a cargo de quem chama.
     */
    public function buscar(string $query, int $startIndex, int $maxResults): array
    {
        $resposta = Http::get($this->baseUrl() . '/volumes', [
            'q'          => $query,
            'startIndex' => $startIndex,
            'maxResults' => $maxResults,
            'orderBy'    => 'relevance',
            'printType'  => 'books',
            'key'        => $this->key(),
        ]);

        if ($resposta->failed()) {
            return ['items' => [], 'totalItems' => 0];
        }

        $dados = $resposta->json();

        return [
            'items'      => $dados['items'] ?? [],
            'totalItems' => $dados['totalItems'] ?? 0,
        ];
    }

    /**
     * Lista paginada de livros de uma categoria/assunto.
     */
    public function porCategoria(string $categoria, int $startIndex, int $maxResults): array
    {
        $resposta = Http::get($this->baseUrl() . '/volumes', [
            'q'          => "subject:{$categoria}",
            'startIndex' => $startIndex,
            'maxResults' => $maxResults,
            'key'        => $this->key(),
        ]);

        if ($resposta->failed()) {
            return ['items' => [], 'totalItems' => 0];
        }

        $dados = $resposta->json();

        return [
            'items'      => $dados['items'] ?? [],
            'totalItems' => $dados['totalItems'] ?? 0,
        ];
    }

    /**
     * Livros populares com nota média alta (>= 4.0), normalizados para a view.
     */
    public function melhoresAvaliados(int $limite = 5): array
    {
        return Cache::remember('home_top_rated_v2', now()->addHours(2), function () use ($limite) {
            try {
                $resposta = Http::timeout(6)->get($this->baseUrl() . '/volumes', [
                    'q'            => 'subject:fiction',
                    'maxResults'   => 30,
                    'orderBy'      => 'relevance',
                    'printType'    => 'books',
                    'langRestrict' => 'en',
                    'key'          => $this->key(),
                ]);

                if ($resposta->failed()) {
                    return [];
                }

                return collect($resposta->json()['items'] ?? [])
                    ->filter(fn ($item) => $this->capasValidas($item)
                        && (($item['volumeInfo']['averageRating'] ?? 0) >= 4))
                    ->map(fn ($item) => $this->normalizar($item))
                    ->sortByDesc('rating')
                    ->take($limite)
                    ->values()
                    ->all();
            } catch (\Throwable) {
                return [];
            }
        });
    }

    /**
     * Converte um item bruto da API para o formato consumido pela view
     * (componente <x-book-card />).
     */
    public function normalizar(array $item): array
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
     * Heurística para filtrar resultados de baixa qualidade:
     *  — precisa ter capa;
     *  — precisa ter ISBN;
     *  — se a data de publicação estiver presente, deve ser >= 1970.
     */
    public function capasValidas(array $item): bool
    {
        $info = $item['volumeInfo'] ?? [];

        // Sem capa, sem ISBN ou sem sinopse → descartado (nunca recomendar).
        if (empty($info['imageLinks'])
            || empty($info['industryIdentifiers'])
            || trim($info['description'] ?? '') === '') {
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
}
