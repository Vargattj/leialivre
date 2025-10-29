<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title', 500);
            $table->string('subtitle', 500)->nullable();
            $table->string('original_title', 500)->nullable();
            $table->string('slug', 500)->unique();
            
            // Publication information
            $table->integer('publication_year')->nullable();
            $table->string('original_publisher')->nullable();
            $table->string('original_language', 50)->default('pt-BR');
            
            // Description and content
            $table->text('synopsis')->nullable();
            $table->longText('full_description')->nullable();
            
            // Technical information
            $table->string('isbn', 20)->nullable();
            $table->integer('pages')->nullable();
            
            // Public domain
            $table->boolean('is_public_domain')->default(true);
            $table->integer('public_domain_year')->nullable();
            $table->text('public_domain_justification')->nullable();
            
            // Images
            $table->string('cover_url', 500)->nullable();
            $table->string('cover_thumbnail_url', 500)->nullable();
            
            // Statistics
            $table->integer('total_downloads')->default(0);
            $table->integer('views')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0.00);
            $table->integer('total_ratings')->default(0);
            
            // Metadata
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('slug');
            $table->index('publication_year');
            $table->index('original_language');
            $table->index('is_featured');
            $table->index('total_downloads');
            $table->index('average_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
