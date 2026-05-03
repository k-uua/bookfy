<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autor_livro', function (Blueprint $table) {
            $table->foreignId('id_autor')->constrained('autores')->cascadeOnDelete();
            $table->foreignId('id_livro')->constrained('livros')->cascadeOnDelete();
            $table->primary(['id_autor', 'id_livro']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autor_livro');
    }
};
