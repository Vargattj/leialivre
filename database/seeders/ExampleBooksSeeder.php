<?php

// ============================================
// database/seeders/ExampleBooksSeeder.php
// ============================================

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use App\Models\File;
use Illuminate\Database\Seeder;

class ExampleBooksSeeder extends Seeder
{
    public function run(): void
    {
        // Create Machado de Assis
        $machado = Author::create([
            'name' => 'Machado de Assis',
            'full_name' => 'Joaquim Maria Machado de Assis',
            'biography' => 'Considered the greatest Brazilian writer of all time.',
            'birth_date' => '1839-06-21',
            'death_date' => '1908-09-29',
            'nationality' => 'Brazil',
        ]);

        // Create Dom Casmurro
        $domCasmurro = Book::create([
            'title' => 'Dom Casmurro',
            'publication_year' => 1899,
            'original_language' => 'pt-BR',
            'synopsis' => 'Narrated in first person by Bento Santiago, the book tells the story of his love for Capitu and the doubt about a possible betrayal.',
            'pages' => 256,
            'is_public_domain' => true,
            'public_domain_year' => 1978,
            'is_featured' => true,
        ]);

        // Relate author and book
        $domCasmurro->authors()->attach($machado->id, [
            'contribution_type' => 'author',
            'order' => 1
        ]);

        // Add category
        $novel = Category::where('name', 'Novel')->first();
        $domCasmurro->categories()->attach($novel->id, ['is_primary' => true]);

        // Add tags
        $tags = ['19th-century', 'brazilian-novel', 'realism', 'betrayal'];
        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $domCasmurro->tags()->attach($tag->id);
        }

        // Add files
        File::create([
            'book_id' => $domCasmurro->id,
            'format' => 'PDF',
            'size_bytes' => 2500000,
            'size_readable' => '2.5 MB',
            'file_url' => 'https://example.com/dom-casmurro.pdf',
            'quality' => 'high',
        ]);

        File::create([
            'book_id' => $domCasmurro->id,
            'format' => 'EPUB',
            'size_bytes' => 1200000,
            'size_readable' => '1.2 MB',
            'file_url' => 'https://example.com/dom-casmurro.epub',
            'quality' => 'high',
        ]);
    }
}