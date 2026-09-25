<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Permite criar o registro despublicado (is_published=false) antes do PDF
    // existir — o fluxo natural é StudyGuide nascer sem arquivo e só publicar
    // depois de fazer upload dele.
    public function up(): void
    {
        Schema::table('study_guides', function (Blueprint $table) {
            $table->string('free_pdf_path')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('study_guides', function (Blueprint $table) {
            $table->string('free_pdf_path')->nullable(false)->change();
        });
    }
};
