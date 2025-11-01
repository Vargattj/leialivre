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
    private function makeRequest(string $url): \Illuminate\Http\Client\Response
    {
        return Http::timeout($this->timeout)
            ->withHeaders([
                'User-Agent' => $this->getUserAgent(),
            ])
            ->get($url);
    }

    /**
     * Fetch biography from Wikipedia
     */
    public function fetchBiography(string $authorName, ?string $wikipediaUrl = null): ?string
    {
        try {
            // Se temos URL da Wikipedia, usar diretamente
            if ($wikipediaUrl) {
                $pageTitle = $this->extractPageTitle($wikipediaUrl);
                if ($pageTitle) {
                    return $this->fetchPageContent($pageTitle);
                }
            }

            // Buscar por nome
            return $this->searchAndFetch($authorName);

        } catch (\Exception $e) {
            Log::error('Error fetching Wikipedia biography', [
                'author' => $authorName,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
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
     * Search author and fetch biography
     */
    private function searchAndFetch(string $authorName): ?string
    {
        try {
            // Tentar em português primeiro
            $bio = $this->fetchPageContent($authorName, 'pt');

            return $bio;

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Fetch Wikipedia page content
     */
    private function fetchPageContent(string $pageTitle, string $lang = 'pt'): ?string
    {
        try {
            // A API REST da Wikipedia aceita títulos com underscores (espaços)
            $pageTitleFormatted = str_replace(' ', '_', $pageTitle);
            $response = $this->makeRequest("https://{$lang}.wikipedia.org/api/rest_v1/page/summary/{$pageTitleFormatted}");

            if ($response->successful()) {
                $data = $response->json();

                // Combinar extract e description
                $biography = '';
                if (! empty($data['extract'])) {
                    $biography = $data['extract'];
                } elseif (! empty($data['description'])) {
                    $biography = $data['description'];
                }

                // Limitar tamanho
                if (strlen($biography) > 5000) {
                    $biography = substr($biography, 0, 5000).'...';
                }

                return $biography ?: null;
            }
        } catch (\Exception $e) {
            Log::warning('Error fetching Wikipedia page', [
                'page' => $pageTitle,
                'lang' => $lang,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
