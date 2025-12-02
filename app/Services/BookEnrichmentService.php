<?php

namespace App\Services;

use App\Services\BookDataSources\CacheManager;
use App\Services\BookDataSources\GutendexDataSource;
use App\Services\BookDataSources\OpenLibraryDataSource;
use App\Services\BookDataSources\GoogleBooksDataSource;
use App\Services\BookDataSources\WikipediaDataSource;
use App\Services\BookMatching\AuthorMatcher;
use App\Services\BookMatching\OpenLibraryMatcher;
use App\Services\BookConsolidation\CategoryConsolidator;
use App\Services\BookConsolidation\TagConsolidator;
use App\Services\BookConsolidation\DescriptionConsolidator;
use App\Services\BookConsolidation\AIContentConsolidator;
use App\Services\BookImport\BookImporter;
use App\Models\Book;
use Illuminate\Support\Facades\Log;

class BookEnrichmentService
{
    private GutendexDataSource $gutendexSource;
    private OpenLibraryDataSource $openLibrarySource;
    private GoogleBooksDataSource $googleBooksSource;
    private WikipediaDataSource $wikipediaSource;
    private OpenLibraryMatcher $openLibraryMatcher;
    private CategoryConsolidator $categoryConsolidator;
    private TagConsolidator $tagConsolidator;
    private DescriptionConsolidator $descriptionConsolidator;
    private AIContentConsolidator $aiContentConsolidator;
    private BookImporter $bookImporter;

    public function __construct(
        ?GutendexDataSource $gutendexSource = null,
        ?OpenLibraryDataSource $openLibrarySource = null,
        ?GoogleBooksDataSource $googleBooksSource = null,
        ?WikipediaDataSource $wikipediaSource = null,
        ?OpenLibraryMatcher $openLibraryMatcher = null,
        ?CategoryConsolidator $categoryConsolidator = null,
        ?TagConsolidator $tagConsolidator = null,
        ?DescriptionConsolidator $descriptionConsolidator = null,
        ?AIContentConsolidator $aiContentConsolidator = null,
        ?BookImporter $bookImporter = null
    ) {
        $this->gutendexSource = $gutendexSource ?? new GutendexDataSource();
        $this->openLibrarySource = $openLibrarySource ?? new OpenLibraryDataSource();
        $this->googleBooksSource = $googleBooksSource ?? new GoogleBooksDataSource();
        $this->wikipediaSource = $wikipediaSource ?? new WikipediaDataSource();
        $this->openLibraryMatcher = $openLibraryMatcher ?? new OpenLibraryMatcher();
        $this->categoryConsolidator = $categoryConsolidator ?? new CategoryConsolidator();
        $this->tagConsolidator = $tagConsolidator ?? new TagConsolidator();
        $this->descriptionConsolidator = $descriptionConsolidator ?? new DescriptionConsolidator();
        $this->aiContentConsolidator = $aiContentConsolidator ?? new AIContentConsolidator();
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
            // Gutendex raw data for storage
            'gutendex_subjects' => $gutendexData['subjects'] ?? [],
            'gutendex_bookshelves' => $gutendexData['bookshelves'] ?? [],
            'gutendex_download_count' => $gutendexData['download_count'] ?? 0,
            'gutendex_summaries' => $gutendexData['summaries'] ?? [],
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
            $enrichedData['gutendex_description'] = $gutendexData['summaries'][0];
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
            
            // Não substituir o nome do autor do Gutenberg - usar apenas o Gutenberg
            // (o tratamento do nome será feito no BookImporter)

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
                    $enrichedData['openlibrary_description'] = is_string($desc) ? $desc : ($desc['value'] ?? '');
                }
                
                // Get cover URLs
                if (!empty($olMatch['cover_i'])) {
                    $coverId = $olMatch['cover_i'];
                    $enrichedData['openlibrary_cover_id'] = $coverId;
                    $enrichedData['openlibrary_cover_thumbnail_url'] = "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg";
                    
                    if (empty($enrichedData['cover_url'])) {
                        $enrichedData['cover_url'] = "https://covers.openlibrary.org/b/id/{$coverId}-L.jpg";
                        $enrichedData['cover_thumbnail_url'] = "https://covers.openlibrary.org/b/id/{$coverId}-M.jpg";
                        Log::info('Cover selected from OpenLibrary', ['cover_id' => $coverId]);
                    }
                }
            }
            
            // Store OpenLibrary raw data
            $enrichedData['openlibrary_isbn'] = !empty($enrichedData['isbn']) ? (is_array($enrichedData['isbn']) ? $enrichedData['isbn'][0] : $enrichedData['isbn']) : null;
            $enrichedData['openlibrary_publisher'] = !empty($enrichedData['publisher']) ? (is_array($enrichedData['publisher']) ? $enrichedData['publisher'][0] : $enrichedData['publisher']) : null;
            $enrichedData['openlibrary_first_publish_year'] = $enrichedData['first_publish_year'] ?? null;
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
            
            // Store Google Books raw data
            $enrichedData['google_books_description'] = $googleBooks['description'] ?? null;
            $enrichedData['google_books_categories'] = $googleBooks['categories'] ?? [];
            $enrichedData['google_books_page_count'] = $googleBooks['pageCount'] ?? null;
            $enrichedData['google_books_average_rating'] = $googleBooks['averageRating'] ?? null;
            $enrichedData['google_books_ratings_count'] = $googleBooks['ratingsCount'] ?? null;
            $enrichedData['google_books_published_date'] = $googleBooks['publishedDate'] ?? null;

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
                $enrichedData['google_books_cover_thumbnail_url'] = $googleBooks['imageLinks']['smallThumbnail'] ?? $googleBooks['imageLinks']['thumbnail'];
                Log::info('Cover selected from Google Books');
            }
        }

        // 4. Fallback: Enrich with Wikipedia (se Google Books não retornou dados suficientes)
        $needsWikipediaFallback = empty($googleBooks) || 
                                  (empty($enrichedData['description_pt']) && empty($enrichedData['cover_url']));
        
        if ($needsWikipediaFallback) {
            Log::info("📖 Searching Wikipedia as fallback...");
            // Construir query cuidadosa: título + autor para encontrar a página correta do livro
            $wikipediaQuery = $title;
            if (!empty($authorName) && $authorName !== 'Unknown') {
                // Usar "Título (autor)" que é o formato comum na Wikipedia
                $wikipediaQuery = "{$title} ({$authorName})";
            }
            
            // Tentar buscar primeiro com o formato completo
            $wikipediaData = $this->wikipediaSource->fetchPageData($wikipediaQuery);
            
            // Se não encontrou, tentar apenas com título + autor sem parênteses
            if (empty($wikipediaData['biography']) && empty($wikipediaData['thumbnail'])) {
                $wikipediaQuery = !empty($authorName) && $authorName !== 'Unknown' 
                    ? "{$title} {$authorName}" 
                    : $title;
                $wikipediaData = $this->wikipediaSource->fetchPageData($wikipediaQuery);
            }
            
            if (!empty($wikipediaData) && (!empty($wikipediaData['biography']) || !empty($wikipediaData['thumbnail']))) {
                $enrichedData['sources'][] = 'wikipedia';
                
                // Store Wikipedia raw data
                $enrichedData['wikipedia_description'] = $wikipediaData['biography'] ?? null;
                $enrichedData['wikipedia_cover_thumbnail_url'] = $wikipediaData['thumbnail'] ?? null;
                
                // Descrição: só usar se Google Books não forneceu
                if (empty($enrichedData['description_pt']) && !empty($wikipediaData['biography'])) {
                    $enrichedData['description_wiki'] = $wikipediaData['biography'];
                    Log::info('Description set from Wikipedia (fallback)');
                }
                
                // Capa: só usar se ainda não temos
                if (empty($enrichedData['cover_url']) && !empty($wikipediaData['thumbnail'])) {
                    $enrichedData['cover_url'] = $wikipediaData['thumbnail'];
                    $enrichedData['cover_thumbnail_url'] = $wikipediaData['thumbnail'];
                    Log::info('Cover selected from Wikipedia (fallback)', ['url' => $wikipediaData['thumbnail']]);
                }
            }
        }
        
        // 4. Final consolidation
        $enrichedData['final_categories'] = $this->categoryConsolidator->consolidate($enrichedData);
        $enrichedData['final_tags'] = $this->tagConsolidator->consolidate($enrichedData);
        
        // Consolidação normal primeiro
        $enrichedData['final_description_pt'] = $this->descriptionConsolidator->consolidate($enrichedData);
        
        // 5. Enriquecimento com IA (se habilitado)
        try {
            $aiContent = $this->aiContentConsolidator->consolidate($enrichedData);
            
            // Descrição
            if (!empty($aiContent['description']) && trim($aiContent['description']) !== trim($enrichedData['final_description_pt'])) {
                $enrichedData['final_description_pt'] = trim($aiContent['description']);
                $enrichedData['final_description_ai'] = trim($aiContent['description']);
                $enrichedData['use_ai'] = $aiContent['used_ai'] ?? false;
                
                if ($enrichedData['use_ai']) {
                    Log::info('Description enriched with AI', [
                        'title' => $enrichedData['title'] ?? 'Unknown',
                        'description_length' => strlen($aiContent['description']),
                    ]);
                }
            } else {
                Log::debug('AI description same as fallback or empty, keeping fallback', [
                    'title' => $enrichedData['title'] ?? 'Unknown',
                ]);
                $enrichedData['use_ai'] = false;
            }

            // Sinopse
            if (!empty($aiContent['synopsis'])) {
                $enrichedData['final_synopsis'] = trim($aiContent['synopsis']);
                
                if ($aiContent['used_ai'] ?? false) {
                    Log::info('Synopsis generated with AI', [
                        'title' => $enrichedData['title'] ?? 'Unknown',
                        'synopsis_length' => strlen($aiContent['synopsis']),
                    ]);
                }
            } else {
                // Fallback: truncar descrição
                $enrichedData['final_synopsis'] = \Illuminate\Support\Str::limit(
                    $enrichedData['final_description_pt'],
                    500
                );
                Log::debug('AI synopsis empty, using fallback truncation');
            }

        } catch (\Exception $e) {
            Log::warning('Failed to enrich content with AI, using fallback', [
                'error' => $e->getMessage(),
                'title' => $enrichedData['title'] ?? 'Unknown',
            ]);
            $enrichedData['use_ai'] = false;
            
            // Fallback sinopse
            $enrichedData['final_synopsis'] = \Illuminate\Support\Str::limit(
                $enrichedData['final_description_pt'],
                500
            );
        }
        
        Log::info("✅ Enrichment complete", [
            'sources' => $enrichedData['sources'],
            'categories' => count($enrichedData['final_categories']),
            'tags' => count($enrichedData['final_tags']),
            'ai_enabled' => $enrichedData['use_ai'] ?? false,
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
