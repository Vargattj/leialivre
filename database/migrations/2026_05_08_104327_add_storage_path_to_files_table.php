<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('files', function (Blueprint $table) {
            // Caminho do arquivo dentro do bucket R2 (ex: books/42/pdf/uuid.pdf)
            $table->string('storage_path', 500)->nullable()->after('file_url');

            // file_url agora é opcional: arquivo pode ter apenas upload no bucket
            $table->string('file_url', 500)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files', function (Blueprint $table) {
            $table->dropColumn('storage_path');
            $table->string('file_url', 500)->nullable(false)->change();
        });
    }
};
