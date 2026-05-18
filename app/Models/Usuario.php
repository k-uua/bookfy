<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuario';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'nome',
        'email',
        'senha',
        'xp',
        'foto_perfil',
    ];

    protected $hidden = [
        'senha',
    ];

    protected function casts(): array
    {
        return [
            'senha' => 'hashed',
        ];
    }

    // ── Auth ─────────────────────────────────────────────────────────────────

    public function getAuthPassword(): string
    {
        return $this->senha;
    }

    public function getAuthPasswordName(): string
    {
        return 'senha';
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    protected function nivel(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) floor(sqrt(($this->xp ?? 0) / 10)),
        );
    }

    // ── Relacionamentos ───────────────────────────────────────────────────────

    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_usuario');
    }

    public function comentariosLivro()
    {
        return $this->hasMany(ComentarioLivro::class, 'id_usuario');
    }

    public function comentariosPostagem()
    {
        return $this->hasMany(ComentarioPostagem::class, 'id_usuario');
    }

    public function estantes()
    {
        return $this->hasMany(Estante::class, 'id_usuario');
    }

    /** Postagens criadas pelo usuário no feed social. */
    public function postagens()
    {
        return $this->hasMany(Postagem::class, 'id_usuario');
    }

    /** Postagens curtidas pelo usuário. */
    public function postagensCurtidas()
    {
        return $this->belongsToMany(
            Postagem::class,
            'postagem_likes',
            'usuario_id',
            'postagem_id'
        );
    }

    public function conquistas()
    {
        return $this->belongsToMany(
            Conquista::class,
            'usuario_conquista',
            'id_usuario',
            'id_conquista'
        );
    }
}
