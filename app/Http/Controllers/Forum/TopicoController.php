<?php

namespace App\Http\Controllers\Forum;

use App\Http\Controllers\Controller;
use App\Http\Requests\Forum\CriarTopicoRequest;
use App\Models\Forum;
use App\Models\Topico;
use Illuminate\Support\Facades\Auth;

class TopicoController extends Controller
{
    /**
     * Cria um novo tópico no fórum.
     */
    public function store(CriarTopicoRequest $request, Forum $forum)
    {
        $topico = Topico::create([
            ...$request->validated(),
            'id_forum'   => $forum->id,
            'id_usuario' => Auth::id(),
        ]);

        return redirect()->route('forum.show', $forum)
            ->with('success', 'Tópico "' . $topico->titulo . '" criado com sucesso!');
    }
}
