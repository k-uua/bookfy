<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nota', function (Blueprint $table) {
            $table->id();
            $table->decimal('nota', 3, 1);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();

            $table->foreignId('id_usuario')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('id_livro')->constrained('livro')->cascadeOnDelete();

            $table->unique(['id_usuario', 'id_livro']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nota');
    }
};
