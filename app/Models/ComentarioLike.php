<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioLike extends Model
{
    protected $table = 'comentario_likes';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    // Chave primária composta — sem coluna de incremento própria.
    protected $primaryKey = null;
    public $incrementing  = false;

    protected $fillable = [
        'id_usuario',
        'id_comentario_livro',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function comentario()
    {
        return $this->belongsTo(ComentarioLivro::class, 'id_comentario_livro');
    }
}
