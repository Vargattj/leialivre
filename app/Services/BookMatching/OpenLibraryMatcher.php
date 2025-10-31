<?php

namespace App\Services\BookMatching;

use App\Services\BookDataSources\OpenLibraryDataSource;
use Illuminate\Support\Str;

class OpenLibraryMatcher
{
    private OpenLibraryDataSource $dataSource;
    private AuthorMatcher $authorMatcher;

    public function __construct(
        ?OpenLibraryDataSource $dataSource = null,
        ?AuthorMatcher $authorMatcher = null
    ) {
        $this->dataSource = $dataSource ?? new OpenLibraryDataSource();
        $this->authorMatcher = $authorMatcher ?? new AuthorMatcher();
    }

    /**
     * Find best OpenLibrary match for a Gutendex book
     */
    public function findBestMatch(array $gutendexBook): ?array
    {
        $title = $gutendexBook['title'] ?? '';
        $authors = $gutendexBook['authors'] ?? [];
        
        if (empty($authors)) {
            return null;
        }
        
        $authorName = $authors[0]['name'] ?? '';
        
        // Search OpenLibrary
        $olResults = $this->dataSource->search($title, $authorName);
        
        $bestMatch = null;
        $bestScore = 0.0;
        
        foreach ($olResults as $result) {
            // Calculate title similarity (accent-insensitive)
            $resultTitle = $result['title'] ?? '';
            $t1 = strtolower(Str::ascii($title));
            $t2 = strtolower(Str::ascii($resultTitle));
            similar_text($t1, $t2, $titlePercent);
            $titleSim = $titlePercent / 100;
            
            // Check author similarity
            $olAuthors = $result['author_name'] ?? [];
            $authorSim = 0.0;
            
            if (!empty($olAuthors)) {
                $authorSims = [];
                foreach ($olAuthors as $olAuthor) {
                    $authorSims[] = $this->authorMatcher->calculateNameSimilarity($authorName, $olAuthor);
                }
                $authorSim = max($authorSims);
            }
            
            // Combined score (higher weight for author)
            $combinedScore = ($titleSim * 0.4) + ($authorSim * 0.6);
            
            if ($combinedScore > $bestScore && $combinedScore > 0.68) {
                $bestScore = $combinedScore;
                $bestMatch = $result;
                $bestMatch['match_score'] = $combinedScore;
            }
        }
        
        return $bestMatch;
    }
}

