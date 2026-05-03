<?php

namespace App\Http\Controllers\Livro;

use App\Http\Controllers\Controller;
use App\Models\Nota;
use App\Http\Requests\Livro\AvaliarLivroRequest;

class NotaController extends Controller
{
    public function avaliar(AvaliarLivroRequest $request)
    {
        Nota::updateOrCreate(
            [
                'id_usuario' => $request->id_usuario,
                'id_livro' => $request->id_livro,
            ],
            [
                'nota' => $request->nota,
            ]
        );

        return redirect()->route('livros.show', ['id' => $request->id_livro])
            ->with('success', 'Nota registrada com sucesso!');
    }
}
