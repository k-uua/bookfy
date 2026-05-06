<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ComentarioLivro extends Model
{
    protected $table = 'comentario_livro';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'texto',
        'id_usuario',
        'id_livro',
        'id_comentario_pai',
    ];

    // ── Relacionamentos ───────────────────────────────────────────────────────

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class, 'id_livro');
    }

    /** Comentário pai (para threads). */
    public function pai()
    {
        return $this->belongsTo(ComentarioLivro::class, 'id_comentario_pai');
    }

    /** Respostas diretas a este comentário. */
    public function respostas()
    {
        return $this->hasMany(ComentarioLivro::class, 'id_comentario_pai');
    }

    /** Likes deste comentário. */
    public function likes()
    {
        return $this->hasMany(ComentarioLike::class, 'id_comentario_livro');
    }

    // ── Listagem ──────────────────────────────────────────────────────────────

    /**
     * Retorna comentários raiz de um livro (sem pai), ordenados do mais recente,
     * com usuário eager-loaded para evitar N+1.
     */
    public static function doLivro(int $livroId): \Illuminate\Database\Eloquent\Collection
    {
        return static::with(['usuario', 'respostas.usuario'])
            ->where('id_livro', $livroId)
            ->whereNull('id_comentario_pai')
            ->latest('criado_em')
            ->get();
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeDoUsuario(Builder $query, int $usuarioId): Builder
    {
        return $query->where('id_usuario', $usuarioId);
    }

    // ── Futura integração: nota do autor do comentário ────────────────────────

    public function notaDoUsuario(): ?Nota
    {
        return Nota::where('id_usuario', $this->id_usuario)
            ->where('id_livro', $this->id_livro)
            ->first();
    }
}
