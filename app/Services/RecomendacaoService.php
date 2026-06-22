<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecomendacaoService
{
    private const CACHE_TTL_MIN = 600; // 10 horas
    private const MINIMO_RECOMENDACOES = 3;

    private const CACHE_DESATIVADO = false;

    public function __construct(
        private GoogleBooksService $googleBooks,
    ) {}

    private function baseUrl(): string
    {
        return rtrim(config('services.recomendacao.url', 'http://localhost:8000'), '/');
    }

    private function token(): string
    {
        return (string) config('services.recomendacao.token', '');
    }

    public static function cacheKey(int $userId): string
    {
        return "recomendados_user_{$userId}";
    }

    /**
     * Invalida o cache de recomendações de um usuário. Chamar após mudanças
     * em interações que alteram o perfil (nota, comentário, favoritar etc).
     */
    public static function invalidar(int $userId): void
    {
        Cache::forget(self::cacheKey($userId));
    }

    /**
     * Consome o serviço Python de recomendações para o usuário e mapeia o
     * resultado para o formato consumido por <x-book-card />.
     *
     * Se o algoritmo personalizado não retornar livros suficientes (cold start:
     * usuário sem interações ou com perfil muito raso), usa "melhores avaliados"
     * como fallback para garantir que a seção da home nunca apareça vazia.
     *
     * Apenas respostas bem-sucedidas e com livros são cacheadas — falhas e
     * resultados vazios não são salvos, garantindo retentativas na próxima
     * requisição.
     */
    public function paraUsuario(int $userId): array
    {
        $cacheKey = self::cacheKey($userId);

        if (! self::CACHE_DESATIVADO && ($cached = Cache::get($cacheKey))) {
            return $cached;
        }

        $livros = $this->buscar($userId);

        if (count($livros) < self::MINIMO_RECOMENDACOES) {
            Log::info('[Recomendacao] Usando fallback (melhores avaliados)', [
                'user_id'         => $userId,
                'total_algoritmo' => count($livros),
                'minimo'          => self::MINIMO_RECOMENDACOES,
            ]);
            $livros = $this->googleBooks->melhoresAvaliados(20);
        }

        if (! self::CACHE_DESATIVADO && ! empty($livros)) {
            Cache::put($cacheKey, $livros, now()->addMinutes(self::CACHE_TTL_MIN));
        }

        return $livros;
    }

    private function buscar(int $userId): array
    {
        $url = $this->baseUrl() . "/recomendacoes/{$userId}";

        $headers = [];
        if ($token = $this->token()) {
            $headers['X-Auth-Token'] = $token;
        }

        try {
            $resposta = Http::timeout(25)->withHeaders($headers)->get($url);

            if (! $resposta->successful()) {
                Log::warning('[Recomendacao] Falha na resposta do serviço Python', [
                    'user_id' => $userId,
                    'url'     => $url,
                    'status'  => $resposta->status(),
                ]);
                return [];
            }

            $livros = collect($resposta->json())
                ->map(fn (array $c) => [
                    'id'         => $c['google_books_id'] ?? null,
                    'titulo'     => $c['titulo'] ?? 'Sem título',
                    'capa'       => $c['thumbnail'] ?? '',
                    'rating'     => round((float) ($c['nota_google'] ?? 0), 1),
                    'autores'    => $c['autores'] ?? [],
                    'lancamento' => null,
                ])
                ->filter(fn ($livro) => ! empty($livro['id']))
                ->values()
                ->all();

            Log::info('[Recomendacao] Conexão bem-sucedida com o serviço Python', [
                'user_id' => $userId,
                'url'     => $url,
                'status'  => $resposta->status(),
                'total'   => count($livros),
            ]);

            return $livros;
        } catch (\Throwable $e) {
            Log::error('[Recomendacao] Exceção ao conectar com o serviço Python', [
                'user_id' => $userId,
                'url'     => $url,
                'erro'    => $e->getMessage(),
            ]);
            return [];
        }
    }
}
