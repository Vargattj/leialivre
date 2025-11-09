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
        
        // Priorizar autores do Gutenberg (authors)
        if (!empty($enrichedData['authors'])) {
            foreach ($enrichedData['authors'] as $index => $authorData) {
                $authorName = is_array($authorData) ? ($authorData['name'] ?? 'Unknown') : $authorData;
                
                // Tratar nome do Gutenberg (converter "Sobrenome, Nome" para "Nome Sobrenome")
                $authorName = $this->normalizeGutenbergAuthorName($authorName);
                
                // Buscar primeiro por slug para evitar duplicatas
                $slug = Str::slug($authorName);
                $author = Author::where('slug', $slug)->first();
                
                if (!$author) {
                    // Se não encontrou pelo slug, buscar pelo nome (case-insensitive)
                    $author = Author::whereRaw('LOWER(name) = ?', [strtolower($authorName)])->first();
                }
                
                if (!$author) {
                    // Se ainda não encontrou, criar novo
                    $author = Author::create([
                        'name' => $authorName,
                        'birth_date' => isset($authorData['birth_year']) ? "{$authorData['birth_year']}-01-01" : null,
                        'death_date' => isset($authorData['death_year']) ? "{$authorData['death_year']}-01-01" : null,
                    ]);
                    $this->enrichAuthorIfNew($author, $authorName, $enrichedData, $index);
                }
                
                $authorModels[] = $author;
            }
        } elseif (!empty($enrichedData['author_name'])) {
            // Fallback: usar author_name do Open Library se não houver autores do Gutenberg
            foreach ($enrichedData['author_name'] as $index => $authorName) {
                // Buscar primeiro por slug para evitar duplicatas
                $slug = Str::slug($authorName);
                $author = Author::where('slug', $slug)->first();
                
                if (!$author) {
                    // Se não encontrou pelo slug, buscar pelo nome (case-insensitive)
                    $author = Author::whereRaw('LOWER(name) = ?', [strtolower($authorName)])->first();
                }
                
                if (!$author) {
                    // Se ainda não encontrou, criar novo
                    $author = Author::create(['name' => $authorName]);
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
                // NÃO atualizar full_name - manter nome do Gutenberg
                $author->update(array_filter([
                    // 'full_name' => NÃO atualizar - manter nome do Gutenberg
                    'pseudonyms' => $enrichedAuthorData['pseudonyms'] ?? null,
                    'biography' => $enrichedAuthorData['biography'] ?? null,
                    'birth_date' => $enrichedAuthorData['birth_date'] ?? null,
                    'death_date' => $enrichedAuthorData['death_date'] ?? null,
                    'nationality' => $enrichedAuthorData['nationality'] ?? null,
                    'photo_url' => $enrichedAuthorData['photo_url'] ?? null,
                    // OpenLibrary fields
                    'openlibrary_description' => $enrichedAuthorData['openlibrary_description'] ?? null,
                    'openlibrary_alternate_names' => $enrichedAuthorData['openlibrary_alternate_names'] ?? null,
                    'openlibrary_birth_date' => $enrichedAuthorData['openlibrary_birth_date'] ?? null,
                    'openlibrary_death_date' => $enrichedAuthorData['openlibrary_death_date'] ?? null,
                    'openlibrary_nationality' => $enrichedAuthorData['openlibrary_nationality'] ?? null,
                    'openlibrary_photo_id' => $enrichedAuthorData['openlibrary_photo_id'] ?? null,
                    'openlibrary_photo_url' => $enrichedAuthorData['openlibrary_photo_url'] ?? null,
                    // Wikipedia fields
                    'wikipedia_biography' => $enrichedAuthorData['wikipedia_biography'] ?? null,
                    'wikipedia_photo_url' => $enrichedAuthorData['wikipedia_photo_url'] ?? null,
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
        // Usar sinopse enriquecida com IA se disponível, senão usar fallback
        $synopsis = !empty($enrichedData['final_synopsis']) 
            ? trim($enrichedData['final_synopsis']) 
            : Str::limit($enrichedData['final_description_pt'] ?? '', 500);
        
        // Usar descrição enriquecida com IA se disponível
        $fullDescription = !empty($enrichedData['final_description_pt']) 
            ? trim($enrichedData['final_description_pt']) 
            : 'Descrição não disponível';
        
        Log::info('Creating book with enriched data', [
            'title' => $enrichedData['title'] ?? 'Unknown',
            'synopsis_length' => strlen($synopsis),
            'description_length' => strlen($fullDescription),
            'ai_used' => $enrichedData['use_ai'] ?? false,
        ]);
        
        return Book::create([
            'title' => $enrichedData['title'],
            'original_language' => $enrichedData['languages'][0] ?? 'en',
            'synopsis' => $synopsis,
            'full_description' => $fullDescription,
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
            // Google Books fields
            'google_books_description' => $enrichedData['google_books_description'] ?? null,
            'google_books_categories' => $enrichedData['google_books_categories'] ?? null,
            'google_books_page_count' => $enrichedData['google_books_page_count'] ?? null,
            'google_books_average_rating' => $enrichedData['google_books_average_rating'] ?? null,
            'google_books_ratings_count' => $enrichedData['google_books_ratings_count'] ?? null,
            'google_books_published_date' => $enrichedData['google_books_published_date'] ?? null,
            'google_books_cover_thumbnail_url' => $enrichedData['google_books_cover_thumbnail_url'] ?? null,
            // OpenLibrary fields
            'openlibrary_description' => $enrichedData['openlibrary_description'] ?? null,
            'openlibrary_isbn' => $enrichedData['openlibrary_isbn'] ?? null,
            'openlibrary_publisher' => $enrichedData['openlibrary_publisher'] ?? null,
            'openlibrary_first_publish_year' => $enrichedData['openlibrary_first_publish_year'] ?? null,
            'openlibrary_cover_id' => $enrichedData['openlibrary_cover_id'] ?? null,
            'openlibrary_cover_thumbnail_url' => $enrichedData['openlibrary_cover_thumbnail_url'] ?? null,
            // Gutendex fields
            'gutendex_description' => $enrichedData['gutendex_description'] ?? null,
            'gutendex_subjects' => $enrichedData['gutendex_subjects'] ?? null,
            'gutendex_bookshelves' => $enrichedData['gutendex_bookshelves'] ?? null,
            'gutendex_download_count' => $enrichedData['gutendex_download_count'] ?? null,
            // Wikipedia fields
            'wikipedia_description' => $enrichedData['wikipedia_description'] ?? null,
            'wikipedia_cover_thumbnail_url' => $enrichedData['wikipedia_cover_thumbnail_url'] ?? null,
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

    /**
     * Normaliza nome do autor do Gutenberg
     * Converte formato "Sobrenome, Nome" para "Nome Sobrenome"
     * 
     * Exemplos:
     * - "Junqueiro, Abílio Manuel Guerra" -> "Abílio Manuel Guerra Junqueiro"
     * - "Abreu, Francisco Jorge de" -> "Francisco Jorge de Abreu"
     * - "Figueiredo, Cândido de" -> "Cândido de Figueiredo"
     * - "Pato, Raimundo António de Bulhão" -> "Raimundo António de Bulhão Pato"
     */
    private function normalizeGutenbergAuthorName(string $name): string
    {
        // Se não contém vírgula, retornar como está
        if (!str_contains($name, ',')) {
            return trim($name);
        }

        // Dividir por vírgula
        $parts = explode(',', $name, 2);
        
        if (count($parts) !== 2) {
            return trim($name);
        }

        $surname = trim($parts[0]);
        $firstName = trim($parts[1]);

        // Se o primeiro nome está vazio, retornar como está
        if (empty($firstName)) {
            return trim($name);
        }

        // Reconstruir no formato "Nome Sobrenome"
        return $firstName . ' ' . $surname;
    }
}

