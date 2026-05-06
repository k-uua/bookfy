<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comentario_topico', function (Blueprint $table) {
            $table->id();
            $table->text('texto');
            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('id_topico')->constrained('topico')->cascadeOnDelete();
            $table->foreignId('id_comentario_pai')
                ->nullable()
                ->constrained('comentario_topico')
                ->nullOnDelete();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentario_topico');
    }
};
