<?php

namespace App\Http\Controllers\Usuario;

use App\Http\Controllers\Controller;
use App\Http\Requests\Usuario\LoginRequest;
use App\Http\Requests\Usuario\RegistroRequest;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        $xp              = $usuario->xp ?? 0;
        $nivel           = $usuario->nivel;
        $xpNivelAtual    = $nivel ** 2 * 10;
        $xpProximoNivel  = ($nivel + 1) ** 2 * 10;
        $xpNoNivel       = $xp - $xpNivelAtual;
        $xpParaSubir     = $xpProximoNivel - $xpNivelAtual;
        $progresso       = $xpParaSubir > 0 ? min(100, (int) ($xpNoNivel / $xpParaSubir * 100)) : 100;

        return view('usuario.perfil', compact(
            'usuario',
            'nivel',
            'xp',
            'xpNoNivel',
            'xpParaSubir',
            'progresso',
        ));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('livros.index');
    }
}
