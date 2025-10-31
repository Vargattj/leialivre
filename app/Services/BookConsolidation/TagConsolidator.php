<?php

namespace App\Services\BookConsolidation;

class TagConsolidator
{
    /**
     * Consolidate tags from multiple sources
     */
    public function consolidate(array $enrichedData): array
    {
        $tags = [];
        
        // Gutenberg subjects
        foreach ($enrichedData['subjects'] ?? [] as $subject) {
            $tag = explode('--', $subject)[0];
            $tag = trim($tag);
            if (strlen($tag) < 50) { // Avoid very long subjects
                $tags[] = $tag;
            }
        }
        
        return array_unique(array_slice($tags, 0, 15));
    }
}

