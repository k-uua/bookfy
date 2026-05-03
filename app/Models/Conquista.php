<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conquista extends Model
{
    protected $table = 'conquistas';

    protected $fillable = [
        'nome',
        'descricao'
    ];

    public function usuarios()
    {
        return $this->belongsToMany(
            Usuario::class,
            'usuario_conquista',
            'id_conquista',
            'id_usuario'
        );
    }
}