<?php

namespace App\Http\Controllers\Livro;

use App\Http\Controllers\Controller;
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
        $query = $request->input('buscar');

        if (empty($query)) {
            return redirect()->route('livros.index');
        }

        $pagina = (int) $request->input('page', 1);
        $porPagina = 10;
        $startIndex = ($pagina - 1) * $porPagina;

        $response = Http::get($this->apiUrl('/volumes'), [
            'q'          => $query,
            'startIndex' => $startIndex,
            'maxResults' => $porPagina,
            'key'        => $this->apiKey(),
        ]);

        if ($response->failed()) {
            return response()->json([
                'erro'   => 'Falha na API externa',
                'status' => $response->status(),
            ]);
        }

        $dados = $response->json();

        $livros = $dados['items'] ?? [];
        $total  = min($dados['totalItems'] ?? 0, 1000);

        $paginacao = new LengthAwarePaginator(
            $livros,
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

        return view('livro.show', [
            'livro'    => $livro,
            'estantes' => $estantes,
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
