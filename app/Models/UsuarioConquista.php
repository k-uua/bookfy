<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsuarioConquista extends Model
{
    protected $table = 'usuario_conquista';

    protected $primaryKey = 'id_conquista';

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = 'atualizado_em';

    protected $fillable = [
        'id_usuario',
        'nivel_conquista',
        'codigo_conquista',
    ];

    // ── Relacionamentos ───────────────────────────────────────────────────────

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    // ── Accessors (dados vindos de config/conquistas.php) ─────────────────────

    /**
     * Retorna o bloco de definição desta conquista a partir do config,
     * resolvendo automaticamente conquistas com ou sem níveis.
     */
    public function getDefinicaoAttribute(): array
    {
        $base = config("conquistas.{$this->codigo_conquista}", []);

        if (!empty($base['possui_niveis'])) {
            $nivelData = $base['niveis'][$this->nivel_conquista] ?? [];
            return array_merge(
                ['titulo' => $base['titulo'] ?? $this->codigo_conquista],
                $nivelData
            );
        }

        return $base;
    }

    public function getTituloAttribute(): string
    {
        return $this->definicao['titulo'] ?? $this->codigo_conquista;
    }

    public function getDescricaoAttribute(): string
    {
        return $this->definicao['descricao'] ?? '';
    }

    public function getIconeAttribute(): string
    {
        return $this->definicao['icone'] ?? '';
    }

    public function getXpAttribute(): int
    {
        return (int) ($this->definicao['xp'] ?? 0);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** Cor Tailwind de acordo com o nível da conquista. */
    public function corNivel(): string
    {
        return match ($this->nivel_conquista) {
            'ouro'   => 'text-amber-400',
            'prata'  => 'text-zinc-300',
            'bronze' => 'text-amber-600',
            default  => 'text-zinc-500',
        };
    }

    /** Emoji de medalha de acordo com o nível. */
    public function medalha(): string
    {
        return match ($this->nivel_conquista) {
            'ouro'   => '🥇',
            'prata'  => '🥈',
            'bronze' => '🥉',
            default  => '🏅',
        };
    }
}
