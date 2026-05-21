<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\AtualizarFotoRequest;
use App\Http\Requests\Usuario\AtualizarPerfilRequest;
use App\Http\Requests\Usuario\LoginRequest;
use App\Http\Requests\Usuario\RegistroRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UsuarioController extends Controller
{
    public function showLogin()
    {
        return view('usuario.login');
    }

    public function login(LoginRequest $request)
    {
        $credenciais = $request->only('email', 'password');

        if (! Auth::attempt($credenciais, $request->boolean('lembrar'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'E-mail ou senha incorretos.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('home'))
            ->with('success', 'Bem-vindo de volta, ' . Auth::user()->nome . '!');
    }

    public function showRegister()
    {
        return view('usuario.registro');
    }

    public function register(RegistroRequest $request)
    {
        $usuario = Usuario::create($request->validated());

        Auth::login($usuario);

        return redirect()->route('home')
            ->with('success', 'Conta criada com sucesso! Bem-vindo, ' . $usuario->nome . '!');
    }

    public function perfil()
    {
        /** @var \App\Models\Usuario $usuario */

        $usuario = Auth::user()->load(['conquistas', 'notas', 'estantes']);
        /** @var \Illuminate\Database\Eloquent\Collection $conquistas */
        $conquistas = $usuario->conquistas;

        $xp              = $usuario->xp ?? 0;
        $nivel           = $usuario->nivel;
        $xpNivelAtual    = $nivel ** 2 * 10;
        $xpProximoNivel  = ($nivel + 1) ** 2 * 10;
        $xpNoNivel       = $xp - $xpNivelAtual;
        $xpParaSubir     = $xpProximoNivel - $xpNivelAtual;
        $progresso       = $xpParaSubir > 0 ? min(100, (int) ($xpNoNivel / $xpParaSubir * 100)) : 100;

        $notasRecentes = $usuario->notas()
            ->with('livro')
            ->latest('criado_em')
            ->take(10)
            ->get();

        $comentariosRecentes = $usuario->comentariosLivro()
            ->with('livro')
            ->whereNull('id_comentario_pai')
            ->latest('criado_em')
            ->take(8)
            ->get();
            
        $livrosFavoritos = $usuario->estantes()
            ->where('nome', 'Favoritos')
            ->with('livros')
            ->first()
            ?->livros
            ->take(8);
        
        return view('usuario.perfil', compact(
            'usuario',
            'nivel',
            'xp',
            'xpNoNivel',
            'xpParaSubir',
            'progresso',
            'conquistas',
            'notasRecentes',
            'comentariosRecentes',
            'livrosFavoritos'
        ));
    }

    public function editarPerfil()
    {
        return view('usuario.editar', ['usuario' => Auth::user()]);
    }

    public function atualizarPerfil(AtualizarPerfilRequest $request)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if ($request->filled('nova_senha')) {
            if (! Hash::check($request->senha_atual, $usuario->senha)) {
                return back()
                    ->withInput($request->except('senha_atual', 'nova_senha', 'nova_senha_confirmation'))
                    ->withErrors(['senha_atual' => 'Senha atual incorreta.']);
            }
        }

        $dados = $request->only('nome', 'email');

        if ($request->filled('nova_senha')) {
            $dados['senha'] = $request->nova_senha;
        }

        $usuario->update($dados);

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }

    public function removerFoto()
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if ($usuario->foto_perfil) {
            Storage::disk('public')->delete($usuario->foto_perfil);
            $usuario->update(['foto_perfil' => null]);
        }

        return back()->with('success', 'Foto removida.');
    }

    public function atualizarFoto(AtualizarFotoRequest $request)
    {
        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        if ($usuario->foto_perfil) {
            Storage::disk('public')->delete($usuario->foto_perfil);
        }

        $path = $request->file('foto')->store('fotos_perfil', 'public');

        $usuario->update(['foto_perfil' => $path]);

        return back()->with('success', 'Foto de perfil atualizada com sucesso!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('livros.index');
    }
}
