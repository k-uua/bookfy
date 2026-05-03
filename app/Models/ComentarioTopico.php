<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComentarioTopico extends Model
{
    protected $table = 'comentario_topico';

    protected $fillable = [
        'texto',
        'id_usuario',
        'id_topico'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function topico()
    {
        return $this->belongsTo(Topico::class, 'id_topico');
    }
}