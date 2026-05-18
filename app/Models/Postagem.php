<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Postagem extends Model
{
    protected $table = 'postagem';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'titulo',
        'conteudo',
        'id_usuario',
        'id_livro',
    ];

    // ── Relacionamentos ──────────────────────────────────────────────────────

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    /** Livro relacionado — pode ser null (postagem sem livro). */
    public function livro()
    {
        return $this->belongsTo(Livro::class, 'id_livro');
    }

    /** Todos os comentários (raiz + respostas). */
    public function comentarios()
    {
        return $this->hasMany(ComentarioPostagem::class, 'id_postagem');
    }

    /** Apenas os comentários raiz (sem pai) — usados no render do feed. */
    public function comentariosRaiz()
    {
        return $this->hasMany(ComentarioPostagem::class, 'id_postagem')
            ->whereNull('id_comentario_pai');
    }

    /** Usuários que curtiram esta postagem. */
    public function curtidoresPor()
    {
        return $this->belongsToMany(
            Usuario::class,
            'postagem_likes',
            'postagem_id',
            'usuario_id'
        );
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /** Verifica se um dado usuário (ou seu id) curtiu esta postagem. */
    public function curtidoPor($usuario): bool
    {
        if (!$usuario) {
            return false;
        }

        $id = is_object($usuario) ? $usuario->id : $usuario;

        return $this->curtidoresPor()->where('usuario_id', $id)->exists();
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    /** Ordena cronologicamente (mais recentes primeiro). */
    public function scopeRecentes(Builder $query): Builder
    {
        return $query->orderByDesc('criado_em');
    }
}
