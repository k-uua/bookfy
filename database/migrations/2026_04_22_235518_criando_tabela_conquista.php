<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conquista', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('descricao');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conquista');
    }
};
