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
            $table->timestamps();

            $table->foreignId('id_usuario')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('id_livro')->constrained('livros')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comentario_livro');
    }
};
