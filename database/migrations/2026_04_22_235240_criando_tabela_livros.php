<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('livros', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('data_lancamento', 20)->nullable();
            $table->text('capa_livro_url')->nullable();
            $table->integer('paginas')->nullable();
            $table->string('google_books_id', 50)->nullable();
            $table->decimal('nota_google_books', 3, 1)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('livros');
    }
};
