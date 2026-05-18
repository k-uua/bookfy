<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postagem', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('conteudo');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();

            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('id_livro')->nullable()->constrained('livro')->nullOnDelete();

            $table->index('id_usuario');
            $table->index('id_livro');
            $table->index('criado_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postagem');
    }
};
