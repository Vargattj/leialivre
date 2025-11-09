<?php

namespace App\Services\BookDataSources;

use App\Services\AI\OpenAIService;
use App\Services\AI\PromptBuilder;
use Illuminate\Support\Facades\Log;

class AuthorEnrichmentService
{
    private OpenLibraryDataSource $openLibrarySource;
    private WikidataDataSource $wikidataSource;
    private WikipediaDataSource $wikipediaSource;
    private OpenAIService $openAIService;
    private PromptBuilder $promptBuilder;

    public function __construct(
        ?OpenLibraryDataSource $openLibrarySource = null,
        ?WikidataDataSource $wikidataSource = null,
        ?WikipediaDataSource $wikipediaSource = null,
        ?OpenAIService $openAIService = null,
        ?PromptBuilder $promptBuilder = null
    ) {
        $this->openLibrarySource = $openLibrarySource ?? new OpenLibraryDataSource();
        $this->wikidataSource = $wikidataSource ?? new WikidataDataSource();
        $this->wikipediaSource = $wikipediaSource ?? new WikipediaDataSource();
        $this->openAIService = $openAIService ?? new OpenAIService();
        $this->promptBuilder = $promptBuilder ?? new PromptBuilder();
    }

    /**
     * Enrich author data from multiple sources
     */
    public function enrichAuthor(string $authorName, ?string $authorKey = null): array
    {
        $enrichedData = [
            'name' => $authorName,
            'sources' => [],
        ];

        // 1. OpenLibrary (prioridade média)
        $olData = $this->fetchOpenLibraryData($authorName, $authorKey);
        if (!empty($olData['source'])) {
            $enrichedData = array_merge($enrichedData, $olData);
            $enrichedData['sources'][] = 'openlibrary';
            // Store OpenLibrary raw data
            $enrichedData['openlibrary_description'] = $olData['biography'] ?? null;
            $enrichedData['openlibrary_alternate_names'] = $olData['pseudonyms'] ?? null;
            $enrichedData['openlibrary_birth_date'] = $olData['birth_date'] ?? null;
            $enrichedData['openlibrary_death_date'] = $olData['death_date'] ?? null;
            $enrichedData['openlibrary_nationality'] = $olData['nationality'] ?? null;
            $enrichedData['openlibrary_photo_id'] = $olData['photo_id'] ?? null;
            $enrichedData['openlibrary_photo_url'] = $olData['photo_url'] ?? null;
        }

        // 2. Wikidata (prioridade alta para dados estruturados)
        // $wikidataData = $this->wikidataSource->searchAuthor($authorName);
        // if (!empty($wikidataData)) {
        //     $enrichedData = $this->mergeAuthorData($enrichedData, $wikidataData, 'wikidata');
        //     $enrichedData['sources'][] = 'wikidata';
        // }

        // 3. Wikipedia (prioridade alta para biografia e foto)
        $wikipediaUrl = null; // $wikidataData['wikipedia_url'] ?? null; // Desabilitado até Wikidata ser implementado
        $wikipediaData = $this->wikipediaSource->fetchPageData($authorName, $wikipediaUrl);
        
        if (!empty($wikipediaData)) {
            // Store Wikipedia raw data
            $enrichedData['wikipedia_biography'] = $wikipediaData['biography'] ?? null;
            $enrichedData['wikipedia_photo_url'] = $wikipediaData['thumbnail'] ?? null;
            
            // Preferir biografia da Wikipedia se ela for mais completa
            if (!empty($wikipediaData['biography'])) {
                $wikipediaBio = $wikipediaData['biography'];
                if (empty($enrichedData['biography']) || strlen($wikipediaBio) > strlen($enrichedData['biography'] ?? '')) {
                    $enrichedData['biography'] = $wikipediaBio;
                }
            }

            // Preferir foto da Wikipedia se disponível (geralmente melhor qualidade)
            if (!empty($wikipediaData['thumbnail'])) {
                $enrichedData['photo_url'] = $wikipediaData['thumbnail'];
                Log::info('Author photo from Wikipedia', ['url' => $wikipediaData['thumbnail']]);
            }

            $enrichedData['sources'][] = 'wikipedia';
        }

        // 4. Enriquecimento de biografia com IA (se habilitado)
        if (!empty($enrichedData['biography']) || !empty($enrichedData['openlibrary_description']) || !empty($enrichedData['wikipedia_biography'])) {
            try {
                $aiBiography = $this->enrichBiographyWithAI($enrichedData);
                if (!empty($aiBiography) && $aiBiography !== ($enrichedData['biography'] ?? '')) {
                    $enrichedData['biography'] = $aiBiography;
                    $enrichedData['biography_ai'] = $aiBiography;
                    $enrichedData['use_ai'] = true;
                    Log::info('Biography enriched with AI', [
                        'author' => $authorName,
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('Failed to enrich biography with AI, using fallback', [
                    'error' => $e->getMessage(),
                    'author' => $authorName,
                ]);
                $enrichedData['use_ai'] = false;
            }
        }

        Log::info("Author enrichment completed", [
            'author' => $authorName,
            'sources' => $enrichedData['sources'],
            'has_biography' => !empty($enrichedData['biography']),
            'has_dates' => !empty($enrichedData['birth_date']) || !empty($enrichedData['death_date']),
            'ai_enabled' => $enrichedData['use_ai'] ?? false,
        ]);

        return $enrichedData;
    }

    /**
     * Enriquecer biografia com IA
     */
    private function enrichBiographyWithAI(array $enrichedData): ?string
    {
        if (!$this->openAIService->isEnabled()) {
            return null;
        }

        try {
            $prompt = $this->promptBuilder->buildBiographyPrompt($enrichedData);
            $cacheKey = $this->openAIService->generateCacheKey('biography', [
                'name' => $enrichedData['name'] ?? '',
                'sources' => $enrichedData['sources'] ?? [],
            ]);

            $biography = $this->openAIService->generateBiography($prompt, $cacheKey);

            if ($biography !== null && !empty(trim($biography))) {
                return trim($biography);
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error enriching biography with AI', [
                'error' => $e->getMessage(),
                'author' => $enrichedData['name'] ?? 'Unknown',
            ]);
            return null;
        }
    }

    /**
     * Fetch data from OpenLibrary
     * Apenas busca se tiver author_key (não busca por nome para manter nome do Gutenberg)
     */
    private function fetchOpenLibraryData(string $authorName, ?string $authorKey): array
    {
        $enrichedData = [];

        // Apenas buscar se temos author_key do OpenLibrary (não buscar por nome)
        if ($authorKey) {
            $authorData = $this->openLibrarySource->fetchAuthor($authorKey);
            if (!empty($authorData)) {
                return $this->mapOpenLibraryAuthorData($authorData, $enrichedData);
            }
        }

        // Não fazer busca por nome - manter nome do Gutenberg
        return $enrichedData;
    }

    /**
     * Merge author data from different sources, prioritizing non-empty values
     */
    private function mergeAuthorData(array $current, array $new, string $source): array
    {
        // Prioridade: Wikidata > OpenLibrary para dados estruturados
        $priorityFields = ['full_name', 'birth_date', 'death_date', 'nationality'];
        
        foreach ($priorityFields as $field) {
            if (empty($current[$field]) && !empty($new[$field])) {
                $current[$field] = $new[$field];
            }
        }

        // Pseudônimos: combinar arrays
        if (!empty($new['pseudonyms'])) {
            $current['pseudonyms'] = array_unique(array_merge(
                $current['pseudonyms'] ?? [],
                is_array($new['pseudonyms']) ? $new['pseudonyms'] : [$new['pseudonyms']]
            ));
        }

        // Foto: preferir OpenLibrary se já temos, senão usar Wikidata se disponível
        if (empty($current['photo_url']) && !empty($new['photo_url'])) {
            $current['photo_url'] = $new['photo_url'];
        }

        return $current;
    }


  
    private function mapOpenLibraryAuthorData(array $olData, array $enrichedData): array
    {
        $enrichedData['source'] = 'openlibrary';

        // Pseudônimos
        if (!empty($olData['alternate_names'])) {
            $enrichedData['pseudonyms'] = $olData['alternate_names'];
        }

        // Biografia
        if (!empty($olData['bio'])) {
            $bio = is_string($olData['bio']) 
                ? $olData['bio'] 
                : ($olData['bio']['value'] ?? '');
            
            // Limitar tamanho da biografia
            if (strlen($bio) > 5000) {
                $bio = substr($bio, 0, 5000) . '...';
            }
            $enrichedData['biography'] = $bio;
        }

        // Datas de nascimento e morte
        if (!empty($olData['birth_date'])) {
            $birthDate = $this->parseDate($olData['birth_date']);
            if ($birthDate) {
                $enrichedData['birth_date'] = $birthDate;
            }
        }

        if (!empty($olData['death_date'])) {
            $deathDate = $this->parseDate($olData['death_date']);
            if ($deathDate) {
                $enrichedData['death_date'] = $deathDate;
            }
        }

        // Nacionalidade
        if (!empty($olData['nationality'])) {
            $nationality = is_array($olData['nationality']) 
                ? $olData['nationality'][0] 
                : $olData['nationality'];
            $enrichedData['nationality'] = $this->normalizeNationality($nationality);
        }

        // Foto
        if (!empty($olData['photos'])) {
            $photoId = is_array($olData['photos']) ? $olData['photos'][0] : $olData['photos'];
            $enrichedData['photo_id'] = $photoId;
            $enrichedData['photo_url'] = "https://covers.openlibrary.org/a/id/{$photoId}-L.jpg";
        }

        return $enrichedData;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate(?string $date): ?string
    {
        if (empty($date)) {
            return null;
        }

        // OpenLibrary pode retornar apenas o ano
        if (preg_match('/^\d{4}$/', $date)) {
            return "{$date}-01-01";
        }

        // Tentar parsear como data completa
        try {
            $parsed = \DateTime::createFromFormat('Y-m-d', $date);
            if ($parsed) {
                return $date;
            }
            
            $parsed = \DateTime::createFromFormat('d/m/Y', $date);
            if ($parsed) {
                return $parsed->format('Y-m-d');
            }
        } catch (\Exception $e) {
            Log::warning("Error parsing date", ['date' => $date, 'error' => $e->getMessage()]);
        }

        return null;
    }

    /**
     * Normalize nationality string
     */
    private function normalizeNationality(string $nationality): string
    {
        $mapping = [
            'Brazilian' => 'Brazil',
            'Brasileiro' => 'Brazil',
            'Brasileira' => 'Brazil',
            'Portuguesa' => 'Portugal',
            'Portuguese' => 'Portugal',
            'Española' => 'Spain',
            'Spanish' => 'Spain',
            'Française' => 'France',
            'French' => 'France',
        ];

        return $mapping[$nationality] ?? $nationality;
    }
}
