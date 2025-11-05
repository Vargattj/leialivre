<?php

namespace App\Services\BookConsolidation;

class CategoryConsolidator
{
    /**
     * Consolidate categories from multiple sources
     */
    public function consolidate(array $enrichedData): array
    {
        $categories = [];
        
        // Google Books categories (translate to Portuguese)
        if (!empty($enrichedData['google_categories'])) {
            $categoryMapping = $this->getCategoryMapping();
            foreach ($enrichedData['google_categories'] as $category) {
                $translated = $this->translateCategory($category, $categoryMapping);
                if ($translated) {
                    $categories[] = $translated;
                } else {
                    // Se não encontrar tradução, manter o original
                    $categories[] = $category;
                }
            }
        }
        
        // Gutenberg bookshelves (convert to Portuguese)
        $shelfMapping = $this->getCategoryMapping();
        
        foreach ($enrichedData['bookshelves'] ?? [] as $shelf) {
            $translated = $this->translateCategory($shelf, $shelfMapping);
            if ($translated) {
                $categories[] = $translated;
            }
        }
        
        return array_unique(array_slice($categories, 0, 5));
    }
    
    /**
     * Get category mapping from English to Portuguese
     */
    private function getCategoryMapping(): array
    {
        return [
            'Fiction' => 'Ficção',
            'Science Fiction' => 'Ficção Científica',
            'Mystery' => 'Mistério',
            'Romance' => 'Romance',
            'Poetry' => 'Poesia',
            "History" => "História",
            "Philosophy" => "Filosofia",
            "Adventure" => "Aventura",
            'Science' => 'Ciência',
            "Novel" => "Romance",
            "Short Story" => "Contos",
            "Theater" => "Teatro",
            "Chronicle" => "Cronologia",
            "Essay" => "Ensaios",
            "Memoirs" => "Memórias",
            "Children's Literature" => "Literatura Infantil",
            "Travel" => "Viagem",
            "Biography" => "Biografia",
            "Autobiography" => "Autobiografia",
            "Essays" => "Ensaios",
            "Mythology" => "Mitologia",
            "Juvenile Fiction" => "Ficção Juvenil",
            "Juvenile Mystery" => "Mistério Juvenil",
            "Juvenile Romance" => "Romance Juvenil",
            "Juvenile Science Fiction" => "Ficção Científica Juvenil",
            "Juvenile Fantasy" => "Fantasia Juvenil",
            "Juvenile Horror" => "Horror Juvenil",
            "Juvenile Thriller" => "Suspense Juvenil",
            "Juvenile Drama" => "Drama Juvenil",
        ];
    }
    
    /**
     * Translate a category from English to Portuguese
     */
    private function translateCategory(string $category, array $mapping): ?string
    {
        // Normalize category name (case-insensitive)
        $categoryLower = strtolower(trim($category));
        
        // Try exact match first
        foreach ($mapping as $en => $pt) {
            if (strtolower($en) === $categoryLower) {
                return $pt;
            }
        }
        
        // Try partial match (contains)
        foreach ($mapping as $en => $pt) {
            if (str_contains($categoryLower, strtolower($en))) {
                return $pt;
            }
        }
        
        return null;
    }
}

