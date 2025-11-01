<?php

namespace App\Services\BookImport;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use App\Models\File;
use App\Services\BookDataSources\AuthorEnrichmentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookImporter
{
    private AuthorEnrichmentService $authorEnrichmentService;

    public function __construct(
        ?AuthorEnrichmentService $authorEnrichmentService = null
    ) {
        $this->authorEnrichmentService = $authorEnrichmentService ?? new AuthorEnrichmentService();
    }
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
            foreach ($enrichedData['author_name'] as $index => $authorName) {
                $author = Author::firstOrCreate(['name' => $authorName]);
                
                // Se o autor foi recém-criado, enriquecer com dados do OpenLibrary
                if ($author->wasRecentlyCreated) {
                    $this->enrichAuthorIfNew($author, $authorName, $enrichedData, $index);
                }
                
                $authorModels[] = $author;
            }
        } else {
            // Caso contrário, usar authors do Gutendex
            foreach ($authors as $index => $authorData) {
                $authorName = is_array($authorData) ? ($authorData['name'] ?? 'Unknown') : $authorData;
                
                $author = Author::firstOrCreate(
                    ['name' => $authorName],
                    [
                        'birth_date' => isset($authorData['birth_year']) ? "{$authorData['birth_year']}-01-01" : null,
                        'death_date' => isset($authorData['death_year']) ? "{$authorData['death_year']}-01-01" : null,
                    ]
                );
                
                // Se o autor foi recém-criado, enriquecer com dados do OpenLibrary
                if ($author->wasRecentlyCreated) {
                    $this->enrichAuthorIfNew($author, $authorName, $enrichedData, $index);
                }
                
                $authorModels[] = $author;
            }
        }
        
        return $authorModels;
    }

    /**
     * Enrich author data if it was just created
     */
    private function enrichAuthorIfNew(Author $author, string $authorName, array $enrichedData, int $index): void
    {
        try {
            // Tentar pegar author_key do OpenLibrary se disponível
            $authorKey = null;
            if (!empty($enrichedData['openlibrary_author_keys'][$index])) {
                $authorKey = '/authors/' . $enrichedData['openlibrary_author_keys'][$index];
            }

            Log::info("Enriching new author", ['author' => $authorName, 'author_key' => $authorKey]);
            
            $enrichedAuthorData = $this->authorEnrichmentService->enrichAuthor($authorName, $authorKey);
            
            if (!empty($enrichedAuthorData['sources'])) {
                // Atualizar o autor com dados enriquecidos
                $author->update(array_filter([
                    'full_name' => $enrichedAuthorData['full_name'] ?? null,
                    'pseudonyms' => $enrichedAuthorData['pseudonyms'] ?? null,
                    'biography' => $enrichedAuthorData['biography'] ?? null,
                    'birth_date' => $enrichedAuthorData['birth_date'] ?? null,
                    'death_date' => $enrichedAuthorData['death_date'] ?? null,
                    'nationality' => $enrichedAuthorData['nationality'] ?? null,
                    'photo_url' => $enrichedAuthorData['photo_url'] ?? null,
                ]));
                
                Log::info("Author enriched successfully", [
                    'author_id' => $author->id,
                    'sources' => $enrichedAuthorData['sources']
                ]);
            }
        } catch (\Exception $e) {
            // Não falhar a importação se o enriquecimento do autor falhar
            Log::warning("Failed to enrich author", [
                'author_id' => $author->id,
                'author_name' => $authorName,
                'error' => $e->getMessage()
            ]);
        }
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

