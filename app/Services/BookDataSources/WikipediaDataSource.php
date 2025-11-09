<?php

namespace App\Services\BookDataSources;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WikipediaDataSource
{
    private int $timeout = 30;

    /**
     * Get user agent for Wikipedia requests
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
                'User-Agent' => $this->getUserAgent(),
            ])
            ->get($url, $params);
    }

    /**
     * Fetch biography from Wikipedia
     */
    public function fetchBiography(string $authorName, ?string $wikipediaUrl = null): ?string
    {
        $pageData = $this->fetchPageData($authorName, $wikipediaUrl);
        return $pageData['biography'] ?? null;
    }

    /**
     * Fetch complete page data from Wikipedia (biography + thumbnail)
     */
    public function fetchPageData(string $query, ?string $wikipediaUrl = null): array
    {
        try {
            $pageTitle = null;
            
            // Se temos URL da Wikipedia, usar diretamente
            if ($wikipediaUrl) {
                $pageTitle = $this->extractPageTitle($wikipediaUrl);
            }

            // Tentar primeiro usar o query diretamente na API REST (mais rápido)
            // Isso evita uma requisição desnecessária se o título já estiver correto
            if (!$pageTitle) {
                $directResult = $this->fetchPageContent($query);
                // Se encontrou dados, retornar diretamente (evita busca)
                if (!empty($directResult['biography']) || !empty($directResult['thumbnail'])) {
                    Log::info('Wikipedia page found via direct query', ['query' => $query]);
                    return $directResult;
                }
            }

            // Se não encontrou diretamente, buscar usando a API de busca para encontrar o título exato
            if (!$pageTitle) {
                $pageTitle = $this->searchPage($query);
            }

            // Se ainda não encontrou, tentar diretamente com o query (última tentativa)
            if (!$pageTitle) {
                $pageTitle = $query;
            }

            return $this->fetchPageContent($pageTitle);

        } catch (\Exception $e) {
            Log::error('Error fetching Wikipedia data', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Search for a Wikipedia page using the search API
     */
    public function searchPage(string $query, string $lang = 'pt'): ?string
    {
        try {
            $response = $this->makeRequest("https://{$lang}.wikipedia.org/w/api.php", [
                'action' => 'query',
                'list' => 'search',
                'srsearch' => $query,
                'srlimit' => 5,
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $results = $data['query']['search'] ?? [];
                
                if (!empty($results)) {
                    // Pegar o primeiro resultado (mais relevante)
                    $firstResult = $results[0];
                    $pageTitle = $firstResult['title'] ?? null;
                    
                    if ($pageTitle) {
                        Log::info('Wikipedia page found via search', [
                            'query' => $query,
                            'page' => $pageTitle,
                        ]);
                        return $pageTitle;
                    }
                }
            }

            // Fallback para inglês se não encontrou em português
            if ($lang === 'pt') {
                return $this->searchPage($query, 'en');
            }

        } catch (\Exception $e) {
            Log::warning('Error searching Wikipedia', [
                'query' => $query,
                'lang' => $lang,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Extract page title from Wikipedia URL
     */
    private function extractPageTitle(string $url): ?string
    {
        if (preg_match('/\/wiki\/(.+)$/', $url, $matches)) {
            return urldecode($matches[1]);
        }

        return null;
    }

    /**
     * Fetch Wikipedia page content (returns array with biography and thumbnail)
     */
    private function fetchPageContent(string $pageTitle, string $lang = 'pt'): array
    {
        $result = ['biography' => null, 'thumbnail' => null];

        try {
            // A API REST da Wikipedia aceita títulos com underscores (espaços)
            $pageTitleFormatted = str_replace(' ', '_', $pageTitle);
            // Codificar URL para lidar com acentos e caracteres especiais
            $pageTitleEncoded = rawurlencode($pageTitleFormatted);
            
            // Tentar português primeiro
            $response = $this->makeRequest("https://{$lang}.wikipedia.org/api/rest_v1/page/summary/{$pageTitleEncoded}");

            if (!$response->successful() && $lang === 'pt') {
                // Fallback para inglês se PT falhar
                $response = $this->makeRequest("https://en.wikipedia.org/api/rest_v1/page/summary/{$pageTitleEncoded}");
            }

            if ($response->successful()) {
                $data = $response->json();

                // Extrair biografia
                if (!empty($data['extract'])) {
                    $biography = $data['extract'];
                } elseif (!empty($data['description'])) {
                    $biography = $data['description'];
                }

                if (!empty($biography)) {
                    // Limitar tamanho
                    if (strlen($biography) > 5000) {
                        $biography = substr($biography, 0, 5000).'...';
                    }
                    $result['biography'] = $biography;
                }

                // Extrair thumbnail
                if (!empty($data['thumbnail']['source'])) {
                    $result['thumbnail'] = $data['thumbnail']['source'];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error fetching Wikipedia page', [
                'page' => $pageTitle,
                'lang' => $lang,
                'error' => $e->getMessage(),
            ]);
        }

        return $result;
    }
}
