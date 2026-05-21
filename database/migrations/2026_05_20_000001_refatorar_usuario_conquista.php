<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Remove a tabela pivot antiga (FK para conquista) antes de dropar conquista
        Schema::dropIfExists('usuario_conquista');
        Schema::dropIfExists('conquista');

        // Recria usuario_conquista centralizada, sem dependência de tabela de conquistas
        Schema::create('usuario_conquista', function (Blueprint $table) {
            $table->id('id_conquista');
            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->enum('nivel_conquista', ['ouro', 'prata', 'bronze']);
            $table->string('codigo_conquista');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();

            // Um usuário não pode ganhar o mesmo código no mesmo nível duas vezes
            $table->unique(['id_usuario', 'codigo_conquista', 'nivel_conquista'], 'uc_usuario_codigo_nivel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_conquista');

        // Recria conquista e usuario_conquista originais para rollback
        Schema::create('conquista', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('descricao');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('usuario_conquista', function (Blueprint $table) {
            $table->foreignId('id_conquista')->constrained('conquista')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->primary(['id_conquista', 'id_usuario']);
        });
    }
};
