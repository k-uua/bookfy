<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topico extends Model
{
    protected $table = 'topicos';

    protected $fillable = [
        'titulo',
        'id_usuario',
        'id_forum',
        'id_livro'
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function forum()
    {
        return $this->belongsTo(Forum::class, 'id_forum');
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class, 'id_livro');
    }

    public function comentarios()
    {
        return $this->hasMany(ComentarioTopico::class, 'id_topico');
    }
}