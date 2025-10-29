<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_author', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('authors')->onDelete('cascade');
            $table->string('contribution_type', 50)->default('author'); // author, translator, organizer, illustrator
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->unique(['book_id', 'author_id', 'contribution_type'], 'book_author_type_unique');
            $table->index('book_id');
            $table->index('author_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_author');
    }
};
