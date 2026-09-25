<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('study_guides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->unique()->constrained('books')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('free_pdf_path');
            $table->string('checkout_url')->nullable();
            $table->unsignedInteger('price_cents')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('study_guides');
    }
};
