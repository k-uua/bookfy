<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estantes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->timestamps();

            $table->foreignId('id_usuario')->constrained('usuarios')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estantes');
    }
};
