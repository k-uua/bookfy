<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioPostagem extends Model
{
    protected $table = 'comentario_postagem';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'texto',
        'id_usuario',
        'id_postagem',
        'id_comentario_pai',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function postagem()
    {
        return $this->belongsTo(Postagem::class, 'id_postagem');
    }

    /** Comentário pai (para threads). */
    public function pai()
    {
        return $this->belongsTo(ComentarioPostagem::class, 'id_comentario_pai');
    }

    /** Respostas diretas a este comentário. */
    public function respostas()
    {
        return $this->hasMany(ComentarioPostagem::class, 'id_comentario_pai');
    }
}
