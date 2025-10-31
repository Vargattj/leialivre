<?php

namespace App\Services\BookDataSources;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GutendexDataSource
{
    private int $timeout = 30;

    public function __construct(
        private ?CacheManager $cacheManager = null
    ) {
        $this->cacheManager = $cacheManager ?? new CacheManager();
    }

    public function fetchBook(int $gutenbergId): array
    {
        $cacheKey = "gutendex_book_{$gutenbergId}";
        
        return $this->cacheManager->remember($cacheKey, 3600, function () use ($gutenbergId) {
            try {
                $response = Http::timeout($this->timeout)
                    ->get("https://gutendex.com/books/{$gutenbergId}");
                
                if ($response->successful()) {
                    return $response->json();
                }
            } catch (\Exception $e) {
                Log::error("Error fetching Gutendex book", [
                    'id' => $gutenbergId,
                    'error' => $e->getMessage()
                ]);
            }
            
            return [];
        });
    }
}

