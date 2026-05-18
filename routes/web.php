<?php

use App\Http\Controllers\Estante\EstanteController;
use App\Http\Controllers\Index\IndexController;
use App\Http\Controllers\Livro\ComentarioLivroController;
use App\Http\Controllers\Livro\LivroController;
use App\Http\Controllers\Livro\NotaController;
use App\Http\Controllers\Postagem\ComentarioPostagemController;
use App\Http\Controllers\Postagem\PostagemController;
use App\Http\Controllers\Usuario\UsuarioController;
use Illuminate\Support\Facades\Route;

Route::prefix('livros')->group(function () {
    Route::get('/', [IndexController::class, 'index'])->name('livros.index');
    Route::get('/buscar', [LivroController::class, 'buscar'])->name('livros.buscar');
    Route::get('/categorias', [LivroController::class, 'categorias'])->name('livros.categorias');
    Route::get('/{id}', [LivroController::class, 'show'])->name('livros.show');
    Route::post('/avaliar', [NotaController::class, 'avaliar'])->name('livros.avaliar')->middleware('auth');
    Route::post('/comentar', [ComentarioLivroController::class, 'comentar'])->name('livros.comentar')->middleware('auth');
    Route::post('/comentarios/{comentario}/responder', [ComentarioLivroController::class, 'responder'])->name('livros.comentarios.responder')->middleware('auth');
    Route::post('/link-compra/{ISBN}', [LivroController::class, 'linkCompra'])->name('livros.linkCompra')->middleware('auth');
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
    Route::get('/perfil/editar', [UsuarioController::class, 'editarPerfil'])->name('usuario.editar');
    Route::put('/perfil', [UsuarioController::class, 'atualizarPerfil'])->name('usuario.atualizar');
    Route::post('/perfil/foto', [UsuarioController::class, 'atualizarFoto'])->name('usuario.foto');
    Route::delete('/perfil/foto', [UsuarioController::class, 'removerFoto'])->name('usuario.foto.remover');
    Route::post('/logout', [UsuarioController::class, 'logout'])->name('usuario.logout');

    Route::prefix('estantes')->group(function () {
        Route::get('/',              [EstanteController::class, 'index'])->name('estante.index');
        Route::post('/',             [EstanteController::class, 'store'])->name('estante.store');
        Route::get('/{estante}',     [EstanteController::class, 'show'])->name('estante.show');
        Route::patch('/{estante}',   [EstanteController::class, 'update'])->name('estante.update');
        Route::delete('/{estante}',  [EstanteController::class, 'destroy'])->name('estante.destroy');
        Route::post('/adicionar',    [EstanteController::class, 'adicionar'])->name('estante.adicionar');
        Route::post('/favoritar',    [EstanteController::class, 'toggleFavorito'])->name('estante.favoritar');
    });

    Route::prefix('postagens')->name('postagens.')->group(function () {
        Route::get('/',                  [PostagemController::class, 'index'])->name('index');
        Route::post('/',                 [PostagemController::class, 'store'])->name('store');
        Route::get('/{postagem}',        [PostagemController::class, 'show'])->name('show');
        Route::delete('/{postagem}',     [PostagemController::class, 'destroy'])->name('destroy');
        Route::post('/{postagem}/like',  [PostagemController::class, 'toggleLike'])->name('like');

        Route::post('/{postagem}/comentarios', [ComentarioPostagemController::class, 'store'])
            ->name('comentarios.store');
        Route::post('/comentarios/{comentario}/responder', [ComentarioPostagemController::class, 'responder'])
            ->name('comentarios.responder');
        Route::delete('/comentarios/{comentario}', [ComentarioPostagemController::class, 'destroy'])
            ->name('comentarios.destroy');
    });
});
