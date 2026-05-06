<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('descricao');
            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum');
    }
};
