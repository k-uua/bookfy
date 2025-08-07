<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;

Route::get('/', function () {
    return view('index');
});

Route::get('/auth/register',[UserController::class, 'register'])->name('auth.register');
Route::get('/index', [BookController::class, 'index'])->name('index');
Route::get('/livros', [BookController::class, 'buscarLivros'])->name('livros.resultados');
Route::get('/categorias', [BookController::class, 'categorias'])->name('livros.categorias');