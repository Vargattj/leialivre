<?php

namespace App\Services\BookDataSources;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleBooksDataSource
{
    private int $timeout = 30;

    public function __construct(
        private ?CacheManager $cacheManager = null
    ) {
        $this->cacheManager = $cacheManager ?? new CacheManager();
    }

    public function search(string $title, string $author): ?array
    {
        $query = 'intitle:' . $title . '+inauthor:' . $author;
        $cacheKey = "google_books_" . md5($query);
        
        return $this->cacheManager->remember($cacheKey, 3600, function () use ($query) {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("https://www.googleapis.com/books/v1/volumes", [
                        'q' => $query,
                        'langRestrict' => 'pt',
                        'filter' => 'free-ebooks',
                        'maxResults' => 3
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $first = $data['items'][0]['volumeInfo'] ?? null;
                    if ($first !== null) {
                        return $first;
                    }
                    // Fallback: retry without langRestrict if nothing found
                    $fallbackResponse = Http::timeout($this->timeout)
                        ->get("https://www.googleapis.com/books/v1/volumes", [
                            'q' => $query,
                            'filter' => 'free-ebooks',
                            'maxResults' => 3
                        ]);
                    if ($fallbackResponse->successful()) {
                        $fallbackData = $fallbackResponse->json();
                        return $fallbackData['items'][0]['volumeInfo'] ?? null;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error searching Google Books", ['error' => $e->getMessage()]);
            }
            
            return null;
        });
    }
}

