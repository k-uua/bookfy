<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topico', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('id_livro')->constrained('livro')->cascadeOnDelete();
            $table->foreignId('id_forum')->constrained('forum')->cascadeOnDelete();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topico');
    }
};
