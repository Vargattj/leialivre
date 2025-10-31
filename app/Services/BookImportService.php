<?php

// ============================================
// app/Services/BookImportService.php
// ============================================

namespace App\Services;

use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Tag;
use App\Models\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookImportService
{
    /**
     * Import book from Open Library API
     */
    public function importFromOpenLibrary(string $openLibraryId)
    {
        try {
            // Get book data from Open Library (normalize id to proper path)
            $path = $this->normalizeOpenLibraryId($openLibraryId);
            $url = "https://openlibrary.org{$path}.json";
            $response = Http::get($url);
            if (!$response->successful()) {
                throw new \Exception("Failed to fetch book from Open Library");
            }

            $data = $response->json();
            
            // Create or find authors
            $authors = [];
            if (isset($data['authors'])) {
                foreach ($data['authors'] as $authorData) {
                    $authorKey = str_replace('/authors/', '', $authorData['author']['key']);
                    $author = $this->importAuthorFromOpenLibrary($authorKey);
                    if ($author) {
                        $authors[] = $author;
                    }
                }
            }

            // Create book
            $book = Book::create([
                'title' => $data['title'] ?? 'Unknown Title',
                'subtitle' => $data['subtitle'] ?? null,
                'original_title' => $data['original_title'] ?? null,
                'publication_year' => $this->extractYear($data),
                'synopsis' => $this->extractDescription($data),
                'is_public_domain' => true,
                'is_featured' => false,
            ]);

            // Attach authors
            foreach ($authors as $index => $author) {
                $book->authors()->attach($author->id, [
                    'contribution_type' => 'author',
                    'order' => $index + 1
                ]);
            }

            // Add subjects as tags
            if (isset($data['subjects'])) {
                foreach (array_slice($data['subjects'], 0, 10) as $subject) {
                    $tag = Tag::firstOrCreate([
                        'name' => Str::limit($subject, 100)
                    ]);
                    $book->tags()->attach($tag->id);
                }
            }

            // Try to get cover image
            if (isset($data['covers']) && !empty($data['covers'])) {
                $coverId = $data['covers'][0];
                $book->cover_url = "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
                $book->cover_thumbnail_url = "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg";
                $book->save();
            }

            Log::info("Book imported successfully", ['book_id' => $book->id, 'title' => $book->title]);
            
            return $book;

        } catch (\Exception $e) {
            Log::error("Error importing book from Open Library", [
                'error' => $e->getMessage(),
                'id' => $openLibraryId
            ]);
            throw $e;
        }
    }

    /**
     * Normalize Open Library id to an API path like /works/OLxxxxW or /books/OLxxxxM
     */
    private function normalizeOpenLibraryId(string $id): string
    {
        $id = trim($id);
        // Remove trailing .json if present
        $id = preg_replace('/\.json$/', '', $id);

        // If already a full path
        if (str_starts_with($id, '/works/') || str_starts_with($id, '/books/')) {
            return $id;
        }
        if (str_starts_with($id, 'works/') || str_starts_with($id, 'books/')) {
            return '/' . $id;
        }

        // If it's a bare OL id, infer type by suffix
        if (preg_match('/^OL\d+[WM]$/', $id)) {
            $suffix = substr($id, -1);
            if ($suffix === 'W') {
                return "/works/{$id}";
            }
            if ($suffix === 'M') {
                return "/books/{$id}";
            }
        }

        // Fallback assume works
        return "/works/{$id}";
    }

    /**
     * Import author from Open Library
     */
    private function importAuthorFromOpenLibrary(string $authorKey)
    {
        try {
            $response = Http::get("https://openlibrary.org/authors/{$authorKey}.json");
            
            if (!$response->successful()) {
                return null;
            }

            $data = $response->json();

            // Check if author already exists
            $author = Author::where('name', $data['name'] ?? '')->first();
            
            if (!$author) {
                $author = Author::create([
                    'name' => $data['name'] ?? 'Unknown Author',
                    'full_name' => $data['fuller_name'] ?? null,
                    'biography' => $data['bio']['value'] ?? $data['bio'] ?? null,
                    'birth_date' => $this->parseDate($data['birth_date'] ?? null),
                    'death_date' => $this->parseDate($data['death_date'] ?? null),
                ]);

                // Get author photo
                if (isset($data['photos']) && !empty($data['photos'])) {
                    $photoId = $data['photos'][0];
                    $author->photo_url = "https://covers.openlibrary.org/a/id/{$photoId}-L.jpg";
                    $author->save();
                }
            }

            return $author;

        } catch (\Exception $e) {
            Log::error("Error importing author", ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Import from Project Gutenberg
     */
    public function importFromGutenberg(int $gutenbergId)
    {
        try {
            // Gutenberg API endpoint
            $response = Http::get("https://gutendex.com/books/{$gutenbergId}");
            
            if (!$response->successful()) {
                throw new \Exception("Failed to fetch book from Gutenberg");
            }

            $data = $response->json();

            // Create or find authors
            $authors = [];
            if (isset($data['authors'])) {
                foreach ($data['authors'] as $authorData) {
                    $author = Author::firstOrCreate(
                        ['name' => $authorData['name']],
                        [
                            'birth_date' => $authorData['birth_year'] ? "{$authorData['birth_year']}-01-01" : null,
                            'death_date' => $authorData['death_year'] ? "{$authorData['death_year']}-01-01" : null,
                        ]
                    );
                    $authors[] = $author;
                }
            }

            // Create book
            $book = Book::create([
                'title' => $data['title'] ?? 'Unknown Title',
                'original_language' => $this->mapLanguage($data['languages'][0] ?? 'en'),
                'synopsis' => null, // Gutenberg doesn't provide descriptions
                'is_public_domain' => true,
                'is_featured' => $data['download_count'] > 1000,
                'total_downloads' => $data['download_count'] ?? 0,
            ]);

            // Attach authors
            foreach ($authors as $index => $author) {
                $book->authors()->attach($author->id, [
                    'contribution_type' => 'author',
                    'order' => $index + 1
                ]);
            }

            // Add subjects as tags
            if (isset($data['subjects'])) {
                foreach (array_slice($data['subjects'], 0, 10) as $subject) {
                    $tag = Tag::firstOrCreate([
                        'name' => Str::limit($subject, 100)
                    ]);
                    $book->tags()->attach($tag->id);
                }
            }

            // Add download files
            if (isset($data['formats'])) {
                $this->addGutenbergFiles($book, $data['formats']);
            }

            // Add cover
            if (isset($data['formats']['image/jpeg'])) {
                $book->cover_url = $data['formats']['image/jpeg'];
                $book->cover_thumbnail_url = $data['formats']['image/jpeg'];
                $book->save();
            }

            Log::info("Book imported from Gutenberg", ['book_id' => $book->id]);
            
            return $book;

        } catch (\Exception $e) {
            Log::error("Error importing from Gutenberg", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Add Gutenberg download files
     */
    private function addGutenbergFiles(Book $book, array $formats)
    {
        $formatMap = [
            'application/epub+zip' => 'EPUB',
            'text/html' => 'HTML',
            'text/plain; charset=utf-8' => 'TXT',
            'application/pdf' => 'PDF',
        ];

        foreach ($formats as $mimeType => $url) {
            if (isset($formatMap[$mimeType])) {
                File::create([
                    'book_id' => $book->id,
                    'format' => $formatMap[$mimeType],
                    'file_url' => $url,
                    'quality' => 'high',
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Import from Domínio Público BR
     */
    public function importFromDominioPublico(string $bookUrl)
    {
        // Note: Domínio Público doesn't have a public API
        // This is a scraping approach (use carefully and respectfully)
        
        try {
            $response = Http::get($bookUrl);
            
            if (!$response->successful()) {
                throw new \Exception("Failed to fetch page");
            }

            $html = $response->body();
            
            // This would require HTML parsing with a library like symfony/dom-crawler
            // Example implementation would be complex and site-specific
            
            // For now, return a placeholder
            throw new \Exception("Domínio Público scraping not yet implemented. Consider manual import.");

        } catch (\Exception $e) {
            Log::error("Error importing from Domínio Público", ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Batch import Brazilian classics
     */
    public function importBrazilianClassics()
    {
        $brazilianBooks = [
            // Open Library IDs for Brazilian classics
            'OL26910874W', // Dom Casmurro
            'OL24997711W', // Memórias Póstumas de Brás Cubas
            'OL26910873W', // Quincas Borba
            // Add more...
        ];

        $imported = [];
        $failed = [];

        foreach ($brazilianBooks as $id) {
            try {
                $book = $this->importFromOpenLibrary($id);
                $imported[] = $book->title;
                
                // Add Brazilian tag
                $brazilTag = Tag::firstOrCreate(['name' => 'Brazilian Literature']);
                $book->tags()->attach($brazilTag->id);
                
                // Add to Novel category
                $novelCategory = Category::where('name', 'Novel')->first();
                if ($novelCategory) {
                    $book->categories()->attach($novelCategory->id, ['is_primary' => true]);
                }

                sleep(1); // Rate limiting - be respectful to APIs
                
            } catch (\Exception $e) {
                $failed[] = $id;
                Log::error("Failed to import Brazilian book", ['id' => $id, 'error' => $e->getMessage()]);
            }
        }

        return [
            'imported' => $imported,
            'failed' => $failed,
        ];
    }

    /**
     * Helper: Extract publication year from various date formats
     */
    private function extractYear($data)
    {
        if (isset($data['first_publish_date'])) {
            preg_match('/\d{4}/', $data['first_publish_date'], $matches);
            return $matches[0] ?? null;
        }
        
        if (isset($data['publish_date'])) {
            preg_match('/\d{4}/', $data['publish_date'], $matches);
            return $matches[0] ?? null;
        }

        return null;
    }

    /**
     * Helper: Extract description
     */
    private function extractDescription($data)
    {
        if (isset($data['description'])) {
            if (is_string($data['description'])) {
                return $data['description'];
            }
            if (isset($data['description']['value'])) {
                return $data['description']['value'];
            }
        }
        return null;
    }

    /**
     * Helper: Parse date string
     */
    private function parseDate($dateString)
    {
        if (!$dateString) {
            return null;
        }

        // Extract year from various formats
        preg_match('/\d{4}/', $dateString, $matches);
        if (!empty($matches)) {
            return $matches[0] . '-01-01';
        }

        return null;
    }

    /**
     * Helper: Map language codes
     */
    private function mapLanguage($code)
    {
        $map = [
            'en' => 'en-US',
            'pt' => 'pt-BR',
            'es' => 'es-ES',
            'fr' => 'fr-FR',
        ];

        return $map[$code] ?? $code;
    }
}