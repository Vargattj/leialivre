<?php

namespace App\Services\BookMatching;

use Illuminate\Support\Str;

class AuthorMatcher
{
    /**
     * Calculate name similarity between two author names
     */
    public function calculateNameSimilarity(string $name1, string $name2): float
    {
        $norm1 = $this->normalizeAuthorName($name1);
        $norm2 = $this->normalizeAuthorName($name2);
        
        // Basic similarity
        similar_text($norm1, $norm2, $percent);
        $basicSim = $percent / 100;
        
        // Check if one name is contained in the other
        if (str_contains($norm1, $norm2) || str_contains($norm2, $norm1)) {
            return max($basicSim, 0.85);
        }
        
        // Check name inversion (First Last vs Last, First)
        $parts1 = explode(' ', $norm1);
        $parts2 = explode(' ', $norm2);
        
        if (count($parts1) >= 2 && count($parts2) >= 2) {
            $reversed1 = end($parts1) . ' ' . $parts1[0];
            $reversed2 = end($parts2) . ' ' . $parts2[0];
            similar_text($reversed1, $reversed2, $reversePercent);
            $basicSim = max($basicSim, $reversePercent / 100);
        }
        
        return $basicSim;
    }
    
    /**
     * Normalize author name for matching
     */
    private function normalizeAuthorName(string $name): string
    {
        // Remove punctuation
        $name = preg_replace('/[.,;:()]/', '', $name);
        
        // Remove extra spaces
        $name = preg_replace('/\s+/', ' ', $name);
        
        // Remove common suffixes
        $suffixes = [' Jr', ' Sr', ' III', ' II', ' Filho', ' Neto'];
        foreach ($suffixes as $suffix) {
            $name = str_replace($suffix, '', $name);
        }
        // Remove diacritics for accent-insensitive comparison
        $name = Str::ascii($name);

        return strtolower(trim($name));
    }
}

