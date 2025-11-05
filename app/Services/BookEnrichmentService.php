<?php

namespace App\Services;

use App\Services\BookDataSources\CacheManager;
use App\Services\BookDataSources\GutendexDataSource;
use App\Services\BookDataSources\OpenLibraryDataSource;
use App\Services\BookDataSources\GoogleBooksDataSource;
use App\Services\BookMatching\AuthorMatcher;
use App\Services\BookMatching\OpenLibraryMatcher;
use App\Services\BookConsolidation\CategoryConsolidator;
use App\Services\BookConsolidation\TagConsolidator;
use App\Services\BookConsolidation\DescriptionConsolidator;
use App\Services\BookImport\BookImporter;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class BookEnrichmentService
{
    private GutendexDataSource $gutendexSource;
    private OpenLibraryDataSource $openLibrarySource;
    private GoogleBooksDataSource $googleBooksSource;
    private OpenLibraryMatcher $openLibraryMatcher;
    private CategoryConsolidator $categoryConsolidator;
    private TagConsolidator $tagConsolidator;
    private DescriptionConsolidator $descriptionConsolidator;
    private BookImporter $bookImporter;

    public function __construct(
        ?GutendexDataSource $gutendexSource = null,
        ?OpenLibraryDataSource $openLibrarySource = null,
        ?GoogleBooksDataSource $googleBooksSource = null,
        ?OpenLibraryMatcher $openLibraryMatcher = null,
        ?CategoryConsolidator $categoryConsolidator = null,
        ?TagConsolidator $tagConsolidator = null,
        ?DescriptionConsolidator $descriptionConsolidator = null,
        ?BookImporter $bookImporter = null
    ) {
        $this->gutendexSource = $gutendexSource ?? new GutendexDataSource();
        $this->openLibrarySource = $openLibrarySource ?? new OpenLibraryDataSource();
        $this->googleBooksSource = $googleBooksSource ?? new GoogleBooksDataSource();
        $this->openLibraryMatcher = $openLibraryMatcher ?? new OpenLibraryMatcher();
        $this->categoryConsolidator = $categoryConsolidator ?? new CategoryConsolidator();
        $this->tagConsolidator = $tagConsolidator ?? new TagConsolidator();
        $this->descriptionConsolidator = $descriptionConsolidator ?? new DescriptionConsolidator();
        $this->bookImporter = $bookImporter ?? new BookImporter();
    }

    /**
     * MAIN ENRICHMENT PIPELINE
     * Complete book enrichment combining multiple sources
     */
    public function enrichBook(int $gutenbergId): array
    {
        $enrichedData = [
            'gutenberg_id' => $gutenbergId,
            'processed_at' => now()->toIso8601String(),
            'sources' => [],
        ];
        
        Log::info("Starting enrichment", ['gutenberg_id' => $gutenbergId]);
        
        // 1. Fetch from Gutendex
        Log::info("📚 Fetching from Gutendex...");
        $gutendexData = $this->gutendexSource->fetchBook($gutenbergId);
        
        if (empty($gutendexData)) {
            throw new \Exception("Book not found on Gutendex");
        }
        
        $enrichedData['sources'][] = 'gutendex';
        $title = $gutendexData['title'] ?? '';
        $authors = $gutendexData['authors'] ?? [];
        $authorName = $authors[0]['name'] ?? 'Unknown';
        
        $enrichedData = array_merge($enrichedData, [
            'title' => $title,
            'authors' => $authors,
            'languages' => $gutendexData['languages'] ?? [],
            'subjects' => $gutendexData['subjects'] ?? [],
            'bookshelves' => $gutendexData['bookshelves'] ?? [],
            'download_links' => $gutendexData['formats'] ?? [],
            'download_count' => $gutendexData['download_count'] ?? 0,
        ]);

        // Preferir capa do Gutenberg se disponível
        if (!empty($gutendexData['formats']['image/jpeg'])) {
            $enrichedData['cover_url'] = $gutendexData['formats']['image/jpeg'];
            $enrichedData['cover_thumbnail_url'] = $gutendexData['formats']['image/jpeg'];
            Log::info('Cover selected from Gutenberg', ['url' => $enrichedData['cover_url']]);
        }

        // Sumarização do Gutenberg (se existir) como fallback em EN
        if (!empty($gutendexData['summaries'][0])) {
            $enrichedData['description_en'] = $gutendexData['summaries'][0];
            Log::info('Description (en) set from Gutenberg summaries');
        }

        // Público domínio a partir do campo copyright do Gutenberg
        if (array_key_exists('copyright', $gutendexData)) {
            $enrichedData['is_public_domain'] = $gutendexData['copyright'] === false;
        }
        
        // 2. Match with OpenLibrary
        Log::info("🔍 Searching OpenLibrary match...");
        $olMatch = $this->openLibraryMatcher->findBestMatch($gutendexData);
        
        if ($olMatch) {
            $enrichedData['sources'][] = 'openlibrary';
            $enrichedData['openlibrary_key'] = $olMatch['key'] ?? null;
            $enrichedData['isbn'] = $olMatch['isbn'] ?? [];
            $enrichedData['publisher'] = $olMatch['publisher'] ?? [];
            $enrichedData['first_publish_year'] = $olMatch['first_publish_year'] ?? null;
            $enrichedData['match_score'] = $olMatch['match_score'] ?? 0;
            
            // Substituir o nome do autor do Gutenberg pelo do OpenLibrary (se disponíveis)
            if (!empty($olMatch['author_name'])) {
                $enrichedData['author_name'] = $olMatch['author_name'];
            }

            // Guardar author_key para enriquecimento posterior
            if (!empty($olMatch['author_key'])) {
                $enrichedData['openlibrary_author_keys'] = $olMatch['author_key'];
            }

            // Fetch work details
            if (!empty($olMatch['key'])) {
                $workDetails = $this->openLibrarySource->fetchWork($olMatch['key']);
                if (!empty($workDetails['description'])) {
                    $desc = $workDetails['description'];
                    $enrichedData['description_en'] = is_string($desc) ? $desc : ($desc['value'] ?? '');
                }
                
                // Get cover URLs
                if (!empty($olMatch['cover_i']) && empty($enrichedData['cover_url'])) {
                    $coverId = $olMatch['cover_i'];
                    $enrichedData['cover_url'] = "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
                    $enrichedData['cover_thumbnail_url'] = "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg";
                    Log::info('Cover selected from OpenLibrary', ['cover_id' => $coverId]);
                }
            }
        }
        
        // 3. Enrich with Google Books
        Log::info("📖 Searching Google Books...");
        $googleBooks = $this->googleBooksSource->search($title, $authorName);
        
        if ($googleBooks) {
            $enrichedData['sources'][] = 'google_books';
            $enrichedData['google_categories'] = $googleBooks['categories'] ?? [];
            $enrichedData['description_pt'] = $googleBooks['description'] ?? null;
            $enrichedData['page_count'] = $googleBooks['pageCount'] ?? null;
            $enrichedData['average_rating'] = $googleBooks['averageRating'] ?? null;
            $enrichedData['ratings_count'] = $googleBooks['ratingsCount'] ?? null;

            // Tentar extrair ano de publicação do Google Books
            if (!empty($googleBooks['publishedDate'])) {
                $year = (int)substr($googleBooks['publishedDate'], 0, 4);
                if ($year > 0) {
                    if (empty($enrichedData['first_publish_year']) || $year < (int)$enrichedData['first_publish_year']) {
                        $enrichedData['first_publish_year'] = $year;
                        Log::info('Publication year set from Google Books', ['year' => $year]);
                    }
                }
            }

            // Se não houver capa ainda, usar a do Google (thumbnail)
            if (!empty($googleBooks['imageLinks']['thumbnail'])) {
                $enrichedData['cover_url'] = $googleBooks['imageLinks']['thumbnail'];
                $enrichedData['cover_thumbnail_url'] = $googleBooks['imageLinks']['smallThumbnail'] ?? $googleBooks['imageLinks']['thumbnail'];
                Log::info('Cover selected from Google Books');
            }
        }
        
        // 4. Final consolidation
        $enrichedData['final_categories'] = $this->categoryConsolidator->consolidate($enrichedData);
        $enrichedData['final_tags'] = $this->tagConsolidator->consolidate($enrichedData);
        $enrichedData['final_description_pt'] = $this->descriptionConsolidator->consolidate($enrichedData);
        
        Log::info("✅ Enrichment complete", [
            'sources' => $enrichedData['sources'],
            'categories' => count($enrichedData['final_categories']),
            'tags' => count($enrichedData['final_tags'])
        ]);
        
        return $enrichedData;
    }
    
    /**
     * Import enriched book to database
     */
    public function importEnrichedBook(array $enrichedData): Book
    {
        return $this->bookImporter->import($enrichedData);
    }
    
    /**
     * Complete pipeline: enrich and import
     */
    public function enrichAndImport(int $gutenbergId): Book
    {
        $enrichedData = $this->enrichBook($gutenbergId);
        return $this->importEnrichedBook($enrichedData);
    }

    // Legacy methods for backwards compatibility
    public function fetchGutendexBook(int $gutenbergId): array
    {
        return $this->gutendexSource->fetchBook($gutenbergId);
    }

    public function searchOpenLibrary(string $title, string $author): array
    {
        return $this->openLibrarySource->search($title, $author);
    }

    public function fetchOpenLibraryWork(string $workKey): array
    {
        return $this->openLibrarySource->fetchWork($workKey);
    }

    public function fetchOpenLibraryAuthor(string $authorKey): array
    {
        return $this->openLibrarySource->fetchAuthor($authorKey);
    }

    public function searchGoogleBooks(string $title, string $author): ?array
    {
        return $this->googleBooksSource->search($title, $author);
    }

    public function findBestOpenLibraryMatch(array $gutendexBook): ?array
    {
        return $this->openLibraryMatcher->findBestMatch($gutendexBook);
    }

    public function calculateNameSimilarity(string $name1, string $name2): float
    {
        $matcher = new AuthorMatcher();
        return $matcher->calculateNameSimilarity($name1, $name2);
    }
}
