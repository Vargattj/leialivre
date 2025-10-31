<?php

namespace App\Services\BookDataSources;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OpenLibraryDataSource
{
    private int $timeout = 30;
    private int $maxRetries = 3;

    public function __construct(
        private ?CacheManager $cacheManager = null
    ) {
        $this->cacheManager = $cacheManager ?? new CacheManager();
    }

    public function search(string $title, string $author): array
    {
        $attempts = [];
        $attempts[] = trim("{$title} {$author}");
        $asciiCombined = trim(Str::ascii("{$title} {$author}"));
        if ($asciiCombined !== end($attempts)) { 
            $attempts[] = $asciiCombined; 
        }
        $attempts[] = trim($title);
        $attempts[] = trim($author);

        foreach ($attempts as $q) {
            if ($q === '') { 
                continue; 
            }

            $cacheKey = "ol_search_" . md5($q);
            $cached = $this->cacheManager->get($cacheKey);
            if ($cached !== null) {
                Log::info('OL search (cache hit)', ['q' => $q, 'docs' => count($cached)]);
                return $cached;
            }

            try {
                Log::info('OL search (request)', ['q' => $q]);
                $response = Http::retry($this->maxRetries, 1000)
                    ->timeout($this->timeout)
                    ->get("https://openlibrary.org/search.json", [
                        'q' => $q,
                        'limit' => 5
                    ]);

                if ($response->successful()) {
                    $docs = $response->json()['docs'] ?? [];
                    Log::info('OL search (response)', ['q' => $q, 'docs' => count($docs)]);
                    if (!empty($docs)) {
                        $this->cacheManager->put($cacheKey, $docs, 3600);
                        return $docs;
                    }
                } else {
                    Log::error('OL search (http error)', ['q' => $q, 'status' => $response->status()]);
                }
            } catch (\Exception $e) {
                Log::error("Error searching OpenLibrary", ['error' => $e->getMessage(), 'q' => $q]);
            }
        }

        return [];
    }

    public function fetchWork(string $workKey): array
    {
        $cacheKey = "ol_work_" . md5($workKey);
        
        $cached = $this->cacheManager->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $response = Http::retry($this->maxRetries, 1000)
                ->timeout($this->timeout)
                ->get("https://openlibrary.org{$workKey}.json");
            
            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data)) {
                    $this->cacheManager->put($cacheKey, $data, 3600);
                }
                return $data ?? [];
            }
        } catch (\Exception $e) {
            Log::error("Error fetching OL work", ['error' => $e->getMessage()]);
        }
        
        return [];
    }

    public function fetchAuthor(string $authorKey): array
    {
        $cacheKey = "ol_author_" . md5($authorKey);
        
        $cached = $this->cacheManager->get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        try {
            $response = Http::retry($this->maxRetries, 1000)
                ->timeout($this->timeout)
                ->get("https://openlibrary.org{$authorKey}.json");
            
            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data)) {
                    $this->cacheManager->put($cacheKey, $data, 3600);
                }
                return $data ?? [];
            }
        } catch (\Exception $e) {
            Log::error("Error fetching OL author", ['error' => $e->getMessage()]);
        }
        
        return [];
    }
}

