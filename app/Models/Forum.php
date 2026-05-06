<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    protected $table = 'forum';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'nome',
        'descricao',
        'id_usuario',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function topicos()
    {
        return $this->hasMany(Topico::class, 'id_forum');
    }
}
