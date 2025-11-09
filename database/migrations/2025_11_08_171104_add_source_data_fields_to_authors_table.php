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
        Schema::table('authors', function (Blueprint $table) {
            // OpenLibrary fields
            $table->text('openlibrary_description')->nullable();
            $table->json('openlibrary_alternate_names')->nullable();
            $table->date('openlibrary_birth_date')->nullable();
            $table->date('openlibrary_death_date')->nullable();
            $table->string('openlibrary_nationality', 100)->nullable();
            $table->integer('openlibrary_photo_id')->nullable();
            $table->string('openlibrary_photo_url', 500)->nullable();

            // Wikipedia fields
            $table->text('wikipedia_biography')->nullable();
            $table->string('wikipedia_photo_url', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authors', function (Blueprint $table) {
            $table->dropColumn([
                'openlibrary_description',
                'openlibrary_alternate_names',
                'openlibrary_birth_date',
                'openlibrary_death_date',
                'openlibrary_nationality',
                'openlibrary_photo_id',
                'openlibrary_photo_url',
                'wikipedia_biography',
                'wikipedia_photo_url',
            ]);
        });
    }
};
