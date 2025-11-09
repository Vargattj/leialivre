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
        Schema::table('books', function (Blueprint $table) {
            // Google Books fields
            $table->text('google_books_description')->nullable();
            $table->json('google_books_categories')->nullable();
            $table->integer('google_books_page_count')->nullable();
            $table->decimal('google_books_average_rating', 3, 2)->nullable();
            $table->integer('google_books_ratings_count')->nullable();
            $table->string('google_books_published_date')->nullable();
            $table->string('google_books_cover_thumbnail_url', 500)->nullable();

            // OpenLibrary fields
            $table->text('openlibrary_description')->nullable();
            $table->string('openlibrary_isbn', 20)->nullable();
            $table->string('openlibrary_publisher')->nullable();
            $table->integer('openlibrary_first_publish_year')->nullable();
            $table->integer('openlibrary_cover_id')->nullable();
            $table->string('openlibrary_cover_thumbnail_url', 500)->nullable();

            // Gutendex fields
            $table->text('gutendex_description')->nullable();
            $table->json('gutendex_subjects')->nullable();
            $table->json('gutendex_bookshelves')->nullable();
            $table->integer('gutendex_download_count')->nullable();

            // Wikipedia fields
            $table->text('wikipedia_description')->nullable();
            $table->string('wikipedia_cover_thumbnail_url', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn([
                'google_books_description',
                'google_books_categories',
                'google_books_page_count',
                'google_books_average_rating',
                'google_books_ratings_count',
                'google_books_published_date',
                'google_books_cover_thumbnail_url',
                'openlibrary_description',
                'openlibrary_isbn',
                'openlibrary_publisher',
                'openlibrary_first_publish_year',
                'openlibrary_cover_id',
                'openlibrary_cover_thumbnail_url',
                'gutendex_description',
                'gutendex_subjects',
                'gutendex_bookshelves',
                'gutendex_download_count',
                'wikipedia_description',
                'wikipedia_cover_thumbnail_url',
            ]);
        });
    }
};
