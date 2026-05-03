<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topicos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->timestamps();

            $table->foreignId('id_usuario')->constrained('usuarios')->cascadeOnDelete();
            $table->foreignId('id_forum')->constrained('foruns')->cascadeOnDelete();

            $table->foreignId('id_livro')
                ->nullable()
                ->constrained('livros')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topicos');
    }
};
