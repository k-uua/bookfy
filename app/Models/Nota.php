<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 'notas';

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
