<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livro extends Model
{
    protected $table = 'livros';

    public $timestamps = false;

    protected $fillable = [
        'titulo',
        'descricao',
        'data_lancamento',
        'capa_livro_url',
        'paginas',
        'google_books_id',
        'nota_google_books',
    ];

    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'autor_livro', 'id_livro', 'id_autor');
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_livro');
    }

    public function comentarios()
    {
        return $this->hasMany(ComentarioLivro::class, 'id_livro');
    }

    public function topicos()
    {
        return $this->hasMany(Topico::class, 'id_livro');
    }
}
