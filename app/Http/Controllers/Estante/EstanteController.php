<?php

namespace App\Http\Controllers\Estante;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estante\AdicionarLivroRequest;
use App\Http\Requests\Estante\EstanteRequest;
use App\Http\Requests\Estante\FavoritoRequest;
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

    public function toggleFavorito(FavoritoRequest $request)
    {
        $dados = $request->validated();

        // Garante que o livro existe localmente.
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

        // Garante que a estante "Favoritos" existe para o usuário.
        $estante = Estante::firstOrCreate([
            'nome'       => 'Favoritos',
            'id_usuario' => Auth::id(),
        ]);

        $jaFavoritado = $estante->livros()
            ->where('livro.id', $livro->id)
            ->exists();

        if ($jaFavoritado) {
            $estante->livros()->detach($livro->id);
            return back()->with('success', '"' . $livro->titulo . '" removido dos favoritos.');
        }

        $estante->livros()->attach($livro->id, [
            'status'   => 'quero_ler',
            'favorito' => 1,
        ]);

        return back()->with('success', '"' . $livro->titulo . '" adicionado aos favoritos!');
    }

    /**
     * Cria uma nova estante vazia para o usuário autenticado.
     */
    public function store(EstanteRequest $request)
    {
        $dados = $request->validated();

        $estante = Estante::create([
            'nome'       => $dados['nome'],
            'id_usuario' => Auth::id(),
        ]);

        return redirect()->route('estante.index')
            ->with('success', 'Estante "' . $estante->nome . '" criada com sucesso!');
    }

    /**
     * Atualiza o nome de uma estante.
     */
    public function update(EstanteRequest $request, Estante $estante)
    {
        abort_if($estante->id_usuario !== Auth::id(), 403);

        $estante->update(['nome' => $request->validated()['nome']]);

        return redirect()->route('estante.index')
            ->with('success', 'Estante renomeada para "' . $estante->nome . '".');
    }

    /**
     * Remove uma estante. Se ela não estiver vazia, a confirmação é exigida
     * pelo frontend antes de chegar aqui — esta ação apenas executa.
     * Os registros da tabela pivô (estante_livro) são removidos via detach,
     * mas os livros em si nunca são apagados (são recursos compartilhados).
     */
    public function destroy(Estante $estante)
    {
        $nome = $estante->nome;
        $estante->livros()->detach();
        $estante->delete();

        return redirect()->route('estante.index')
            ->with('success', 'Estante "' . $nome . '" removida.');
    }
}
