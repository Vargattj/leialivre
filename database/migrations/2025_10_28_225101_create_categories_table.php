<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// ============================================
// database/migrations/2024_01_01_000002_create_categories_table.php
// ============================================

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->string('slug', 100)->unique();
            $table->foreignId('parent_category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('parent_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};