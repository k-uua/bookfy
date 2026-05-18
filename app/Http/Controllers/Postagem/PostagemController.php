<?php

namespace App\Http\Controllers\Postagem;

use App\Http\Controllers\Controller;
use App\Http\Requests\Postagem\CriarPostagemRequest;
use App\Models\Postagem;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostagemController extends Controller
{
    /**
     * Feed social — todas as postagens, mais recentes primeiro.
     */
    public function index(Request $request)
    {
        $postagens = Postagem::with(['usuario', 'livro'])
            ->withCount(['comentarios', 'curtidoresPor as likes_count'])
            ->orderByDesc('likes_count')
            ->paginate(20);

        $topPostagens = Postagem::with('usuario')
            ->withCount('curtidoresPor as likes_count')
            ->orderByDesc('likes_count')
            ->limit(5)
            ->get();

        $topUsuarios = Usuario::select('usuario.*')
            ->selectRaw('COUNT(DISTINCT postagem.id) as total_posts')
            ->selectRaw('COUNT(postagem_likes.usuario_id) as total_likes')
            ->join('postagem', 'postagem.id_usuario', '=', 'usuario.id')
            ->leftJoin('postagem_likes', 'postagem_likes.postagem_id', '=', 'postagem.id')
            ->groupBy('usuario.id')
            ->orderByDesc('total_likes')
            ->limit(5)
            ->get();

        return view('postagem.index', compact('postagens', 'topPostagens', 'topUsuarios'));
    }

    /**
     * Página de uma única postagem com toda a thread de comentários.
     */
    public function show(Postagem $postagem)
    {
        $postagem->load([
            'usuario',
            'livro',
            'comentariosRaiz.usuario',
            'comentariosRaiz.respostas.usuario',
        ]);

        $postagem->loadCount([
            'comentarios',
            'curtidoresPor as likes_count',
        ]);

        $curtidaPeloUsuario = Auth::check()
            ? $postagem->curtidoPor(Auth::id())
            : false;

        return view('postagem.show', compact('postagem', 'curtidaPeloUsuario'));
    }

    /**
     * Cria uma nova postagem.
     */
    public function store(CriarPostagemRequest $request)
    {
        $postagem = Postagem::create([
            ...$request->validated(),
            'id_usuario' => Auth::id(),
        ]);

        return redirect()->route('postagens.show', $postagem)
            ->with('success', 'Postagem publicada!');
    }

    /**
     * Remove uma postagem — apenas o autor pode deletar.
     */
    public function destroy(Postagem $postagem)
    {
        abort_if($postagem->id_usuario !== Auth::id(), 403);

        $postagem->delete();

        return redirect()->route('postagens.index')
            ->with('success', 'Postagem removida.');
    }

    /**
     * Alterna a curtida de uma postagem para o usuário autenticado.
     * Toggle: se já curtiu, descurte; se não, curte. Impede duplicação.
     */
    public function toggleLike(Postagem $postagem)
    {
        $usuarioId = Auth::id();

        if ($postagem->curtidoPor($usuarioId)) {
            $postagem->curtidoresPor()->detach($usuarioId);
        } else {
            $postagem->curtidoresPor()->attach($usuarioId);
        }

        return back();
    }
}
