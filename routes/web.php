<?php

use App\Http\Controllers\Estante\EstanteController;
use App\Http\Controllers\Livro\ComentarioLivroController;
use App\Http\Controllers\Livro\LivroController;
use App\Http\Controllers\Livro\NotaController;
use App\Http\Controllers\Usuario\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('livros')->group(function () {
    Route::get('/', [LivroController::class, 'index'])->name('livros.index');
    Route::get('/buscar', [LivroController::class, 'buscar'])->name('livros.buscar');
    Route::get('/categorias', [LivroController::class, 'categorias'])->name('livros.categorias');
    Route::get('/{id}', [LivroController::class, 'show'])->name('livros.show');
    Route::post('/avaliar', [NotaController::class, 'avaliar'])->name('livros.avaliar')->middleware('auth');
    Route::post('/comentar', [ComentarioLivroController::class, 'comentar'])->name('livros.comentar')->middleware('auth');
    Route::post('/comentarios/{comentario}/responder', [ComentarioLivroController::class, 'responder'])->name('livros.comentarios.responder')->middleware('auth');
    Route::delete('/comentarios/{comentario}', [ComentarioLivroController::class, 'deletar'])->name('livros.comentarios.deletar')->middleware('auth');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [UsuarioController::class, 'showLogin'])->name('usuario.login');
    Route::post('/login', [UsuarioController::class, 'login'])->name('usuario.autenticar');
    Route::get('/registro', [UsuarioController::class, 'showRegister'])->name('usuario.registro');
    Route::post('/registro', [UsuarioController::class, 'register'])->name('usuario.registrar');
});

Route::middleware('auth')->group(function () {
    Route::get('/home', [LivroController::class, 'home'])->name('home');
    Route::get('/perfil', [UsuarioController::class, 'perfil'])->name('usuario.perfil');
    Route::post('/logout', [UsuarioController::class, 'logout'])->name('usuario.logout');

    Route::prefix('estantes')->group(function () {
        Route::get('/', [EstanteController::class, 'index'])->name('estante.index');
        Route::get('/{estante}', [EstanteController::class, 'show'])->name('estante.show');
        Route::post('/adicionar', [EstanteController::class, 'adicionar'])->name('estante.adicionar');
    });
});
