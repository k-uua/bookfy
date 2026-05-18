<?php

namespace App\Http\Controllers\Postagem;

use App\Http\Controllers\Controller;
use App\Http\Requests\Postagem\ComentarioPostagemRequest;
use App\Models\ComentarioPostagem;
use App\Models\Postagem;
use Illuminate\Support\Facades\Auth;

class ComentarioPostagemController extends Controller
{
    /**
     * Adiciona um comentário raiz a uma postagem.
     */
    public function store(ComentarioPostagemRequest $request, Postagem $postagem)
    {
        ComentarioPostagem::create([
            'texto'       => $request->validated()['texto'],
            'id_usuario'  => Auth::id(),
            'id_postagem' => $postagem->id,
        ]);

        return back()->with('success', 'Comentário publicado!');
    }

    /**
     * Adiciona uma resposta a um comentário existente (threading).
     * O livro/postagem é herdado do pai — sem dados redundantes no body.
     */
    public function responder(ComentarioPostagemRequest $request, ComentarioPostagem $comentario)
    {
        ComentarioPostagem::create([
            'texto'             => $request->validated()['texto'],
            'id_usuario'        => Auth::id(),
            'id_postagem'       => $comentario->id_postagem,
            'id_comentario_pai' => $comentario->id,
        ]);

        return back()->with('success', 'Resposta publicada!');
    }

    /**
     * Remove um comentário — apenas o autor pode deletar.
     * Respostas-filhas terão id_comentario_pai setado para NULL pelo FK
     * (nullOnDelete na migração), o que as transforma em comentários raiz.
     */
    public function destroy(ComentarioPostagem $comentario)
    {
        abort_if($comentario->id_usuario !== Auth::id(), 403);

        $comentario->delete();

        return back()->with('success', 'Comentário removido.');
    }
}
