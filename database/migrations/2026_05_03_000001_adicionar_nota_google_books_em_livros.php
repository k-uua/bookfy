<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Esta migration foi absorvida pela migration base de livro.
// Mantida apenas para compatibilidade com histórico de migrate:status.
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('livro') && ! Schema::hasColumn('livro', 'nota_google_books')) {
            Schema::table('livro', function (Blueprint $table) {
                $table->decimal('nota_google_books', 3, 1)->nullable()->after('google_books_id');
            });
        }
    }

    public function down(): void {}
};
