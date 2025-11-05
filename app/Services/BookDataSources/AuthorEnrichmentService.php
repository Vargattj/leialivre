<?php

namespace App\Services\BookDataSources;

use Illuminate\Support\Facades\Log;

class AuthorEnrichmentService
{
    private OpenLibraryDataSource $openLibrarySource;
    private WikidataDataSource $wikidataSource;
    private WikipediaDataSource $wikipediaSource;

    public function __construct(
        ?OpenLibraryDataSource $openLibrarySource = null,
        ?WikidataDataSource $wikidataSource = null,
        ?WikipediaDataSource $wikipediaSource = null
    ) {
        $this->openLibrarySource = $openLibrarySource ?? new OpenLibraryDataSource();
        $this->wikidataSource = $wikidataSource ?? new WikidataDataSource();
        $this->wikipediaSource = $wikipediaSource ?? new WikipediaDataSource();
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
        }

        // 2. Wikidata (prioridade alta para dados estruturados)
        // $wikidataData = $this->wikidataSource->searchAuthor($authorName);
        // if (!empty($wikidataData)) {
        //     $enrichedData = $this->mergeAuthorData($enrichedData, $wikidataData, 'wikidata');
        //     $enrichedData['sources'][] = 'wikidata';
        // }

        // 3. Wikipedia (prioridade alta para biografia e foto)
        $wikipediaUrl = $wikidataData['wikipedia_url'] ?? null;
        $wikipediaData = $this->wikipediaSource->fetchPageData($authorName, $wikipediaUrl);
        
        if (!empty($wikipediaData)) {
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

        Log::info("Author enrichment completed", [
            'author' => $authorName,
            'sources' => $enrichedData['sources'],
            'has_biography' => !empty($enrichedData['biography']),
            'has_dates' => !empty($enrichedData['birth_date']) || !empty($enrichedData['death_date']),
        ]);

        return $enrichedData;
    }

    /**
     * Fetch data from OpenLibrary
     */
    private function fetchOpenLibraryData(string $authorName, ?string $authorKey): array
    {
        $enrichedData = [];

        // Se temos author_key do OpenLibrary, buscar diretamente
        if ($authorKey) {
            $authorData = $this->openLibrarySource->fetchAuthor($authorKey);
            if (!empty($authorData)) {
                return $this->mapOpenLibraryAuthorData($authorData, $enrichedData);
            }
        }

        // Caso contrário, buscar por nome
        $authorData = $this->searchAuthorByName($authorName);
        if (!empty($authorData)) {
            return $this->mapOpenLibraryAuthorData($authorData, $enrichedData);
        }

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

    /**
     * Search author by name on OpenLibrary
     */
    private function searchAuthorByName(string $authorName): array
    {
        try {
            // Buscar obras do autor para encontrar a chave do autor
            $works = $this->openLibrarySource->search($authorName, '');
            
            if (empty($works)) {
                return [];
            }

            // Pegar o primeiro author_key encontrado
            $firstWork = $works[0] ?? null;
            if (empty($firstWork['author_key'])) {
                return [];
            }

            $authorKey = '/authors/' . $firstWork['author_key'][0];
            return $this->openLibrarySource->fetchAuthor($authorKey);
            
        } catch (\Exception $e) {
            Log::error("Error searching author by name", [
                'author' => $authorName,
                'error' => $e->getMessage()
            ]);
            return [];
        }
    }

    /**
     * Map OpenLibrary author data to our format
     */
    private function mapOpenLibraryAuthorData(array $olData, array $enrichedData): array
    {
        $enrichedData['source'] = 'openlibrary';
        
        // Nome completo
        if (!empty($olData['name'])) {
            $enrichedData['full_name'] = $olData['name'];
        }

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
