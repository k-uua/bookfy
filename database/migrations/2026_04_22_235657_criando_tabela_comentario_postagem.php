<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Comentários (com suporte a threading via id_comentario_pai)
        Schema::create('comentario_postagem', function (Blueprint $table) {
            $table->id();
            $table->text('texto');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();

            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('id_postagem')->constrained('postagem')->cascadeOnDelete();
            $table->foreignId('id_comentario_pai')
                ->nullable()
                ->constrained('comentario_postagem')
                ->nullOnDelete();

            $table->index('id_postagem');
            $table->index('id_comentario_pai');
        });

        // Curtidas — chave composta evita duplicação (1 like por usuário/postagem)
        Schema::create('postagem_likes', function (Blueprint $table) {
            $table->foreignId('postagem_id')->constrained('postagem')->cascadeOnDelete();
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->timestamp('criado_em')->useCurrent();

            $table->primary(['postagem_id', 'usuario_id']);
            $table->index('usuario_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postagem_likes');
        Schema::dropIfExists('comentario_postagem');
    }
};
