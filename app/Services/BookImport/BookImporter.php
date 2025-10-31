<?php

namespace App\Services\BookImport;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use App\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookImporter
{
    /**
     * Import enriched book to database
     */
    public function import(array $enrichedData): Book
    {
        DB::beginTransaction();
            
        try {
            // 1. Create or find authors
            $authorModels = $this->createOrFindAuthors($enrichedData);
            
            // 2. Create book
            $book = $this->createBook($enrichedData);
            
            // 3. Attach authors
            $this->attachAuthors($book, $authorModels);
            
            // 4. Add categories
            $this->attachCategories($book, $enrichedData);
            
            // 5. Add tags
            $this->attachTags($book, $enrichedData);
            
            // 6. Add download files
            $this->attachFiles($book, $enrichedData);
            
            DB::commit();
            
            Log::info("Book imported successfully", [
                'book_id' => $book->id,
                'title' => $book->title,
                'sources' => $enrichedData['sources']
            ]);
            
            return $book;
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error importing book", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    private function createOrFindAuthors(array $enrichedData): array
    {
        $authorModels = [];
        $authors = $enrichedData['author_name'] ?? $enrichedData['authors'] ?? [];
        
        // Se author_name do OL existe, usar ele
        if (!empty($enrichedData['author_name'])) {
            foreach ($enrichedData['author_name'] as $authorName) {
                $author = Author::firstOrCreate(['name' => $authorName]);
                $authorModels[] = $author;
            }
        } else {
            // Caso contrário, usar authors do Gutendex
            foreach ($authors as $authorData) {
                $authorName = is_array($authorData) ? ($authorData['name'] ?? 'Unknown') : $authorData;
                
                $author = Author::firstOrCreate(
                    ['name' => $authorName],
                    [
                        'birth_date' => isset($authorData['birth_year']) ? "{$authorData['birth_year']}-01-01" : null,
                        'death_date' => isset($authorData['death_year']) ? "{$authorData['death_year']}-01-01" : null,
                    ]
                );
                
                $authorModels[] = $author;
            }
        }
        
        return $authorModels;
    }

    private function createBook(array $enrichedData): Book
    {
        return Book::create([
            'title' => $enrichedData['title'],
            'original_language' => $enrichedData['languages'][0] ?? 'en',
            'synopsis' => Str::limit($enrichedData['final_description_pt'], 500),
            'full_description' => $enrichedData['final_description_pt'],
            'publication_year' => $enrichedData['first_publish_year'] ?? null,
            'pages' => $enrichedData['page_count'] ?? null,
            'isbn' => !empty($enrichedData['isbn']) ? (is_array($enrichedData['isbn']) ? $enrichedData['isbn'][0] : $enrichedData['isbn']) : null,
            'cover_url' => $enrichedData['cover_url'] ?? null,
            'cover_thumbnail_url' => $enrichedData['cover_thumbnail_url'] ?? null,
            'is_public_domain' => $enrichedData['is_public_domain'] ?? true,
            'public_domain_year' => $enrichedData['first_publish_year'] ?? null,
            'total_downloads' => $enrichedData['download_count'] ?? 0,
            'average_rating' => $enrichedData['average_rating'] ?? 0,
            'total_ratings' => $enrichedData['ratings_count'] ?? 0,
            'is_featured' => ($enrichedData['download_count'] ?? 0) > 1000,
        ]);
    }

    private function attachAuthors(Book $book, array $authorModels): void
    {
        foreach ($authorModels as $index => $author) {
            $book->authors()->attach($author->id, [
                'contribution_type' => 'author',
                'order' => $index + 1
            ]);
        }
    }

    private function attachCategories(Book $book, array $enrichedData): void
    {
        foreach ($enrichedData['final_categories'] as $index => $categoryName) {
            $category = Category::firstOrCreate(['name' => $categoryName]);
            $book->categories()->attach($category->id, [
                'is_primary' => $index === 0
            ]);
        }
    }

    private function attachTags(Book $book, array $enrichedData): void
    {
        foreach ($enrichedData['final_tags'] as $tagName) {
            $tag = Tag::firstOrCreate(['name' => $tagName]);
            $book->tags()->attach($tag->id);
            $tag->increment('usage_count');
        }
    }

    private function attachFiles(Book $book, array $enrichedData): void
    {
        $formatMapping = [
            'application/epub+zip' => 'EPUB',
            'text/html' => 'HTML',
            'text/plain; charset=utf-8' => 'TXT',
            'application/pdf' => 'PDF',
        ];
        
        foreach ($enrichedData['download_links'] ?? [] as $mimeType => $url) {
            if (isset($formatMapping[$mimeType])) {
                File::create([
                    'book_id' => $book->id,
                    'format' => $formatMapping[$mimeType],
                    'file_url' => $url,
                    'quality' => 'high',
                    'is_active' => true,
                ]);
            }
        }
    }
}

