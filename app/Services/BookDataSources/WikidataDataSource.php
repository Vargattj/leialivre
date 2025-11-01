<?php

namespace App\Services\BookDataSources;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WikidataDataSource
{
    private int $timeout = 30;

    /**
     * Get user agent for Wikidata requests
     */
    private function getUserAgent(): string
    {
        $appName = config('app.name', 'LeiaLivre');
        $contactEmail = config('mail.from.address', 'contact@leialivre.com');
        return "{$appName}/1.0 (https://leialivre.com; contact: {$contactEmail})";
    }

    /**
     * Make HTTP request with proper user agent
     */
    private function makeRequest(string $url, array $params = []): \Illuminate\Http\Client\Response
    {
        return Http::timeout($this->timeout)
            ->withHeaders([
                'User-Agent' => $this->getUserAgent()
            ])
            ->get($url, $params);
    }

    /**
     * Search author on Wikidata
     */
    public function searchAuthor(string $authorName): ?array
    {
        try {
            // Buscar entidade no Wikidata
            $query = urlencode($authorName);
            $response = $this->makeRequest("https://www.wikidata.org/w/api.php", [
                'action' => 'wbsearchentities',
                'search' => $authorName,
                'language' => 'pt',
                'format' => 'json',
                'limit' => 5
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $entities = $data['search'] ?? [];
                
                if (empty($entities)) {
                    return null;
                }

                // Pegar a primeira entidade encontrada
                $entityId = $entities[0]['id'] ?? null;
                if ($entityId) {
                    return $this->fetchEntityData($entityId);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error searching Wikidata", [
                'author' => $authorName,
                'error' => $e->getMessage()
            ]);
        }

        return null;
    }

    /**
     * Fetch entity data from Wikidata
     */
    private function fetchEntityData(string $entityId): array
    {
        try {
            $response = $this->makeRequest("https://www.wikidata.org/w/api.php", [
                'action' => 'wbgetentities',
                'ids' => $entityId,
                'languages' => 'pt|en',
                'props' => 'claims|descriptions|labels|sitelinks',
                'format' => 'json'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $entity = $data['entities'][$entityId] ?? null;
                
                if ($entity) {
                    return $this->parseEntityData($entity);
                }
            }
        } catch (\Exception $e) {
            Log::error("Error fetching Wikidata entity", [
                'entity_id' => $entityId,
                'error' => $e->getMessage()
            ]);
        }

        return [];
    }

    /**
     * Parse Wikidata entity data
     */
    private function parseEntityData(array $entity): array
    {
        $data = [];

        // Nome
        if (!empty($entity['labels']['pt']['value'])) {
            $data['full_name'] = $entity['labels']['pt']['value'];
        } elseif (!empty($entity['labels']['en']['value'])) {
            $data['full_name'] = $entity['labels']['en']['value'];
        }

        // Descrição/Biografia
        if (!empty($entity['descriptions']['pt']['value'])) {
            $data['biography'] = $entity['descriptions']['pt']['value'];
        } elseif (!empty($entity['descriptions']['en']['value'])) {
            $data['biography'] = $entity['descriptions']['en']['value'];
        }

        // Links para Wikipedia
        if (!empty($entity['sitelinks']['ptwiki']['url'])) {
            $data['wikipedia_url'] = $entity['sitelinks']['ptwiki']['url'];
        } elseif (!empty($entity['sitelinks']['enwiki']['url'])) {
            $data['wikipedia_url'] = $entity['sitelinks']['enwiki']['url'];
        }

        // Datas de nascimento e morte
        if (!empty($entity['claims']['P569'])) { // birth date
            $birth = $entity['claims']['P569'][0]['mainsnak']['datavalue']['value']['time'] ?? null;
            if ($birth) {
                $data['birth_date'] = $this->parseWikidataDate($birth);
            }
        }

        if (!empty($entity['claims']['P570'])) { // death date
            $death = $entity['claims']['P570'][0]['mainsnak']['datavalue']['value']['time'] ?? null;
            if ($death) {
                $data['death_date'] = $this->parseWikidataDate($death);
            }
        }

        // Nacionalidade
        if (!empty($entity['claims']['P27'])) { // nationality
            $nationality = $entity['claims']['P27'][0]['mainsnak']['datavalue']['value']['id'] ?? null;
            if ($nationality) {
                $data['nationality'] = $this->getCountryLabel($nationality);
            }
        }

        // Local de nascimento
        if (!empty($entity['claims']['P19'])) { // place of birth
            $place = $entity['claims']['P19'][0]['mainsnak']['datavalue']['value']['id'] ?? null;
            if ($place) {
                $data['birth_place'] = $this->getPlaceLabel($place);
            }
        }

        return $data;
    }

    /**
     * Parse Wikidata date format (e.g., +1829-05-01T00:00:00Z)
     */
    private function parseWikidataDate(string $date): ?string
    {
        if (preg_match('/\+(\d{4})-(\d{2})-(\d{2})/', $date, $matches)) {
            return "{$matches[1]}-{$matches[2]}-{$matches[3]}";
        }
        return null;
    }

    /**
     * Get country label from Wikidata ID
     */
    private function getCountryLabel(string $entityId): string
    {
        $countryMapping = [
            'Q155' => 'Brazil',      // Brasil
            'Q45' => 'Portugal',     // Portugal
            'Q29' => 'Spain',        // Espanha
            'Q142' => 'France',      // França
            'Q30' => 'United States', // Estados Unidos
            'Q145' => 'United Kingdom', // Reino Unido
        ];

        return $countryMapping[$entityId] ?? 'Unknown';
    }

    /**
     * Get place label from Wikidata ID
     */
    private function getPlaceLabel(string $entityId): string
    {
        // Por enquanto retornar vazio, pode ser expandido depois
        return '';
    }
}

