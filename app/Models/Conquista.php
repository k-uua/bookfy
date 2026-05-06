<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conquista extends Model
{
    protected $table = 'conquista';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'nome',
        'descricao',
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
