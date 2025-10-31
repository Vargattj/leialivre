<?php

namespace App\Services\BookConsolidation;

class DescriptionConsolidator
{
    /**
     * Get best available description in Portuguese
     */
    public function consolidate(array $enrichedData): string
    {
        // Priority: Google Books PT > OpenLibrary EN > Gutenberg summaries > fallback
        if (!empty($enrichedData['description_pt'])) {
            return $enrichedData['description_pt'];
        }
        
        if (!empty($enrichedData['description_en'])) {
            return $enrichedData['description_en'];
        }
        
        // Fallback: generate from subjects
        if (!empty($enrichedData['subjects'])) {
            $subjects = array_slice($enrichedData['subjects'], 0, 3);
            return "Obra clássica sobre: " . implode(', ', $subjects);
        }
        
        return 'Descrição não disponível';
    }
}

