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
        
        // Google Books categories
        if (!empty($enrichedData['google_categories'])) {
            $categories = array_merge($categories, $enrichedData['google_categories']);
        }
        
        // Gutenberg bookshelves (convert to Portuguese)
        $shelfMapping = [
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
        ];
        
        foreach ($enrichedData['bookshelves'] ?? [] as $shelf) {
            foreach ($shelfMapping as $en => $pt) {
                if (str_contains($shelf, $en)) {
                    $categories[] = $pt;
                    break;
                }
            }
        }
        
        return array_unique(array_slice($categories, 0, 5));
    }
}

