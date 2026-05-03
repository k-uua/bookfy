<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estante extends Model
{
    protected $table = 'estantes';

    protected $fillable = [
        'nome',
        'id_usuario'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function livros()
    {
        return $this->belongsToMany(
            Livro::class,
            'estante_livro',
            'id_estante',
            'id_livro'
        )->withPivot('status', 'favorito');
    }
}