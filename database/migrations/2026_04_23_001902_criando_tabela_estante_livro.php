<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estante_livro', function (Blueprint $table) {
            $table->foreignId('id_estante')->constrained('estantes')->cascadeOnDelete();
            $table->foreignId('id_livro')->constrained('livros')->cascadeOnDelete();
            $table->enum('status', ['quero_ler', 'lendo', 'lido'])->default('quero_ler');
            $table->tinyInteger('favorito')->default(0);
            $table->primary(['id_estante', 'id_livro']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estante_livro');
    }
};
