<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario_conquista', function (Blueprint $table) {
            $table->foreignId('id_conquista')->constrained('conquistas')->cascadeOnDelete();
            $table->foreignId('id_usuario')->constrained('usuarios')->cascadeOnDelete();
            $table->primary(['id_conquista', 'id_usuario']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_conquista');
    }
};
