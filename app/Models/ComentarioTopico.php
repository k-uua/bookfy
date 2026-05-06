<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioTopico extends Model
{
    protected $table = 'comentario_topico';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'texto',
        'id_usuario',
        'id_topico',
        'id_comentario_pai',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function topico()
    {
        return $this->belongsTo(Topico::class, 'id_topico');
    }

    /** Comentário pai (para threads). */
    public function pai()
    {
        return $this->belongsTo(ComentarioTopico::class, 'id_comentario_pai');
    }

    /** Respostas diretas a este comentário. */
    public function respostas()
    {
        return $this->hasMany(ComentarioTopico::class, 'id_comentario_pai');
    }
}
