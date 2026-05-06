<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 'nota';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'nota',
        'id_usuario',
        'id_livro',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class, 'id_livro');
    }
}
