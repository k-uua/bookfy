<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notas', function (Blueprint $table) {
            $table->id();
            $table->decimal('nota', 3, 1);
            $table->timestamps();

            $table->foreignId('id_usuario')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('id_livro')->constrained('livros')->cascadeOnDelete();

            $table->unique(['id_usuario', 'id_livro']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notas');
    }
};
