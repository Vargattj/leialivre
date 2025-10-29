<?php

// ============================================
// database/migrations/2024_01_01_000001_create_authors_table.php
// ============================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('full_name', 500)->nullable();
            $table->json('pseudonyms')->nullable();
            $table->text('biography')->nullable();
            $table->date('birth_date')->nullable();
            $table->date('death_date')->nullable();
            $table->string('nationality', 100)->nullable();
            $table->string('photo_url', 500)->nullable();
            $table->string('slug')->unique();
            $table->timestamps();
            
            $table->index('nationality');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};






