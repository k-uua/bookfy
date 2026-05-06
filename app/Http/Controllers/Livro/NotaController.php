<?php

namespace App\Http\Controllers\Livro;

use App\Http\Controllers\Controller;
use App\Http\Requests\Livro\AvaliarLivroRequest;
use App\Models\Livro;
use App\Models\Nota;
use Illuminate\Support\Facades\Auth;

class NotaController extends Controller
{
    public function avaliar(AvaliarLivroRequest $request)
    {
        $dados = $request->validated();

        // Garante que o livro existe localmente antes de registrar a nota.
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

        Nota::updateOrCreate(
            ['id_usuario' => Auth::id(), 'id_livro' => $livro->id],
            ['nota' => $dados['nota']]
        );

        return back()->with('success', 'Sua avaliação foi registrada!');
    }
}
