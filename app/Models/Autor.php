<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    protected $table = 'autores';

    protected $fillable = [
        'nome'
    ];

    public $timestamps = false; 

    public function livros()
    {
        return $this->belongsToMany(
            Livro::class,
            'autor_livro',
            'id_autor',
            'id_livro'
        );
    }
}