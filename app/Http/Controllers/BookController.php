<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BookController extends Controller
{

    public function index()
    {
        return view('index');
    }
    public function buscarLivros(Request $request)
    {
        $query = $request->input('buscar');
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        $startIndex = ($pagina - 1) * $porPagina;

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => $query,
            'startIndex' => $startIndex,
            'maxResults' => $porPagina,
        ]);

        $dados = $response->json();

        $livros = $dados['items'] ?? [];
        $total = $dados['totalItems'] ?? 0;

        // Montar paginação manual
        $paginacao = new \Illuminate\Pagination\LengthAwarePaginator(
            $livros,
            $total,
            $porPagina,
            $pagina,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('livros.resultados', ['livros' => $paginacao]);
    }

    public function categorias(Request $request)
    {
        $query = $request->input('categoria');
        $pagina = $request->input('page', 1);
        $porPagina = 10;
        $startIndex = ($pagina - 1) * $porPagina;

        $response = Http::get('https://www.googleapis.com/books/v1/volumes', [
            'q' => 'subject:' . $query,
            'startIndex' => $startIndex,
            'maxResults' => $porPagina
        ]);

        $dados = $response->json();

        $livros = $dados['items'] ?? [];
        $total = $dados['totalItems'] ?? 0;

        $paginacao = new \Illuminate\Pagination\LengthAwarePaginator(
            $livros,
            $total,
            $porPagina,
            $pagina,
            ['path' => url()->current(), 'query' => $request->query()]
        );

        return view('livros.resultados', ['livros' => $paginacao]);
    }
}
