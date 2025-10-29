<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->string('format', 20); // PDF, EPUB, MOBI, TXT
            $table->bigInteger('size_bytes')->nullable();
            $table->string('size_readable', 50)->nullable(); // "2.5 MB"
            $table->string('file_url', 500);
            $table->string('backup_url', 500)->nullable();
            $table->string('md5_hash', 32)->nullable();
            $table->string('quality', 50)->nullable(); // high, medium, scanned
            $table->integer('total_downloads')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('book_id');
            $table->index('format');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};