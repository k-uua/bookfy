<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\CriarForumRequest;
use App\Models\Forum;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    /**
     * Lista de fóruns + destaques.
     * "Fóruns em alta" são ordenados pela quantidade de tópicos.
     */
    public function index()
    {
        $forunsEmAlta = Forum::with('usuario')
            ->withCount('topicos')
            ->orderByDesc('topicos_count')
            ->orderByDesc('criado_em')
            ->take(5)
            ->get();

        return view('forum.index', compact('forunsEmAlta'));
    }

    public function create()
    {
        return view('forum.create');
    }

    /**
     * Cria um novo fórum associado ao usuário autenticado.
     */
    public function store(CriarForumRequest $request)
    {
        $forum = Forum::create([
            ...$request->validated(),
            'id_usuario' => Auth::id(),
        ]);

        return redirect()->route('forum.index')
            ->with('success', 'Fórum "' . $forum->nome . '" criado com sucesso!');
    }

    /**
     * Página de um fórum específico, com seu cabeçalho e a lista de tópicos.
     */
    public function show(Forum $forum)
    {
        $forum->load('usuario');

        $topicos = $forum->topicos()
            ->with('usuario')
            ->withCount('comentarios')
            ->latest('criado_em')
            ->get();

        return view('forum.show', compact('forum', 'topicos'));
    }
}
