<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentario_livro', function (Blueprint $table) {
            $table->id();
            $table->text('texto');
            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('id_livro')->constrained('livro')->cascadeOnDelete();
            $table->foreignId('id_comentario_pai')
                ->nullable()
                ->constrained('comentario_livro')
                ->nullOnDelete();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('comentario_likes', function (Blueprint $table) {
            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('id_comentario_livro')->constrained('comentario_livro')->cascadeOnDelete();
            $table->primary(['id_usuario', 'id_comentario_livro']);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentario_likes');
        Schema::dropIfExists('comentario_livro');
    }
};
