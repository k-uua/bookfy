<?php

namespace App\Http\Controllers\Estante;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estante\AdicionarLivroRequest;
use App\Models\Estante;
use App\Models\Livro;
use Illuminate\Support\Facades\Auth;

class EstanteController extends Controller
{
    public function index()
    {
        $estantes = Auth::user()
            ->estantes()
            ->withCount('livros')
            ->orderBy('criado_em', 'desc')
            ->get();

        return view('estante.index', compact('estantes'));
    }

    public function show(Estante $estante)
    {
        abort_if($estante->id_usuario !== Auth::id(), 403);

        $livros = $estante->livros()->get();

        return view('estante.show', compact('estante', 'livros'));
    }

    public function adicionar(AdicionarLivroRequest $request)
    {
        $dados = $request->validated();

        // 1. Garante que o livro existe localmente; cria se ainda não tiver sido salvo.
        $livro = Livro::firstOrCreate(
            ['google_books_id' => $dados['google_books_id']],
            [
                'titulo'            => $dados['titulo'],
                'capa_livro_url'    => $dados['capa_livro_url'] ?? null,
                'descricao'         => $dados['descricao'] ?? null,
                'paginas'           => $dados['paginas'] ?? null,
                'data_lancamento'   => $dados['data_lancamento'] ?? null,
                'nota_google_books' => $dados['nota_google_books'] ?? null,
            ]
        );

        // 2. Garante que a estante com esse nome existe para o usuário autenticado; cria se necessário.
        $estante = Estante::firstOrCreate([
            'nome'       => $dados['nome_estante'],
            'id_usuario' => Auth::id(),
        ]);

        // 3. Verifica se o livro já está na estante para evitar duplicatas.
        $jaAdicionado = $estante->livros()
            ->where('livro.id', $livro->id)
            ->exists();

        if ($jaAdicionado) {
            return back()->with('info', '"' . $livro->titulo . '" já está na estante "' . $estante->nome . '".');
        }

        // 4. Adiciona o livro na estante com status inicial "quero_ler".
        $estante->livros()->attach($livro->id, [
            'status'   => 'quero_ler',
            'favorito' => 0,
        ]);

        return back()->with('success', '"' . $livro->titulo . '" adicionado à estante "' . $estante->nome . '"!');
    }
}
