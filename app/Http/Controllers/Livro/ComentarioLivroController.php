<?php

namespace App\Http\Controllers\Livro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Livro\ComentarioLivroRequest;
use App\Models\ComentarioLivro;
use App\Models\Livro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComentarioLivroController extends Controller
{
    /**
     * Salva um novo comentário no livro.
     * Garante que o livro exista localmente (firstOrCreate) antes de comentar.
     */
    public function comentar(ComentarioLivroRequest $request)
    {
        $dados = $request->validated();

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

        ComentarioLivro::create([
            'texto'      => $dados['texto'],
            'id_usuario' => Auth::id(),
            'id_livro'   => $livro->id,
        ]);

        return back()->with('success', 'Comentário publicado!');
    }

    /**
     * Publica uma resposta a um comentário existente.
     * O livro é herdado do comentário pai — sem necessidade de firstOrCreate.
     */
    public function responder(ComentarioLivro $comentario, Request $request)
    {
        $dados = $request->validate([
            'texto' => ['required', 'string', 'min:3', 'max:1000'],
        ]);

        ComentarioLivro::create([
            'texto'              => $dados['texto'],
            'id_usuario'         => Auth::id(),
            'id_livro'           => $comentario->id_livro,
            'id_comentario_pai'  => $comentario->id,
        ]);

        return back()->with('success', 'Resposta publicada!');
    }

    /**
     * Remove um comentário.
     * Apenas o autor do comentário pode deletar.
     */
    public function deletar(ComentarioLivro $comentario)
    {
        abort_if($comentario->id_usuario !== Auth::id(), 403);

        $comentario->delete();

        return back()->with('success', 'Comentário removido.');
    }
}
