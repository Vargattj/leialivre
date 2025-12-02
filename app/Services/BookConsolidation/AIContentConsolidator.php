<?php

namespace App\Services\BookConsolidation;

use App\Services\AI\OpenAIService;
use App\Services\AI\PromptBuilder;
use Illuminate\Support\Facades\Log;

class AIContentConsolidator
{
    private OpenAIService $openAIService;
    private PromptBuilder $promptBuilder;
    private DescriptionConsolidator $fallbackDescriptionConsolidator;

    public function __construct(
        ?OpenAIService $openAIService = null,
        ?PromptBuilder $promptBuilder = null,
        ?DescriptionConsolidator $fallbackDescriptionConsolidator = null
    ) {
        $this->openAIService = $openAIService ?? new OpenAIService();
        $this->promptBuilder = $promptBuilder ?? new PromptBuilder();
        $this->fallbackDescriptionConsolidator = $fallbackDescriptionConsolidator ?? new DescriptionConsolidator();
    }

    /**
     * Consolida descrição e gera sinopse usando IA
     * Retorna array com ['description' => string, 'synopsis' => string]
     */
    public function consolidate(array $enrichedData): array
    {
        $result = [
            'description' => null,
            'synopsis' => null,
            'used_ai' => false,
        ];

        // Se IA não está habilitada, usar fallback
        if (!$this->openAIService->isEnabled()) {
            Log::debug('OpenAI not enabled, using fallback consolidation');
            $result['description'] = $this->fallbackDescriptionConsolidator->consolidate($enrichedData);
            $result['synopsis'] = $this->generateFallbackSynopsis($result['description']);
            return $result;
        }

        try {
            $prompt = $this->promptBuilder->buildContentPrompt($enrichedData);
            $cacheKey = $this->openAIService->generateCacheKey('content', [
                'title' => $enrichedData['title'] ?? '',
                'sources' => $enrichedData['sources'] ?? [],
            ]);

            $content = $this->openAIService->generateContent($prompt, $cacheKey);

            if ($content !== null) {
                Log::info('Content generated with AI', [
                    'title' => $enrichedData['title'] ?? 'Unknown',
                ]);
                
                $result['description'] = trim($content['description']);
                $result['synopsis'] = trim($content['synopsis']);
                $result['used_ai'] = true;
                
                return $result;
            }

            // Fallback se IA retornar vazio ou erro
            Log::warning('AI returned empty content, using fallback');
            $result['description'] = $this->fallbackDescriptionConsolidator->consolidate($enrichedData);
            $result['synopsis'] = $this->generateFallbackSynopsis($result['description']);
            return $result;

        } catch (\Exception $e) {
            Log::error('Error consolidating content with AI', [
                'error' => $e->getMessage(),
                'title' => $enrichedData['title'] ?? 'Unknown',
            ]);

            // Fallback em caso de erro
            $result['description'] = $this->fallbackDescriptionConsolidator->consolidate($enrichedData);
            $result['synopsis'] = $this->generateFallbackSynopsis($result['description']);
            return $result;
        }
    }

    private function generateFallbackSynopsis(string $description): string
    {
        if (empty($description)) {
            return 'Sinopse não disponível';
        }

        if (strlen($description) <= 500) {
            return $description;
        }

        $truncated = substr($description, 0, 497);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }
}
