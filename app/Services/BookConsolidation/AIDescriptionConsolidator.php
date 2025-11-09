<?php

namespace App\Services\BookConsolidation;

use App\Services\AI\OpenAIService;
use App\Services\AI\PromptBuilder;
use Illuminate\Support\Facades\Log;

class AIDescriptionConsolidator
{
    private OpenAIService $openAIService;
    private PromptBuilder $promptBuilder;
    private DescriptionConsolidator $fallbackConsolidator;

    public function __construct(
        ?OpenAIService $openAIService = null,
        ?PromptBuilder $promptBuilder = null,
        ?DescriptionConsolidator $fallbackConsolidator = null
    ) {
        $this->openAIService = $openAIService ?? new OpenAIService();
        $this->promptBuilder = $promptBuilder ?? new PromptBuilder();
        $this->fallbackConsolidator = $fallbackConsolidator ?? new DescriptionConsolidator();
    }

    /**
     * Consolida descrições usando IA
     * Retorna descrição enriquecida ou fallback para consolidação normal
     */
    public function consolidateWithAI(array $enrichedData): string
    {
        // Se IA não está habilitada, usar fallback
        if (!$this->openAIService->isEnabled()) {
            Log::debug('OpenAI not enabled, using fallback consolidation');
            return $this->fallbackConsolidator->consolidate($enrichedData);
        }

        try {
            $prompt = $this->promptBuilder->buildDescriptionPrompt($enrichedData);
            $cacheKey = $this->openAIService->generateCacheKey('description', [
                'title' => $enrichedData['title'] ?? '',
                'sources' => $enrichedData['sources'] ?? [],
            ]);

            $description = $this->openAIService->generateDescription($prompt, $cacheKey);

            if ($description !== null && !empty(trim($description))) {
                Log::info('Description enriched with AI', [
                    'title' => $enrichedData['title'] ?? 'Unknown',
                ]);
                return trim($description);
            }

            // Fallback se IA retornar vazio
            Log::warning('AI returned empty description, using fallback');
            return $this->fallbackConsolidator->consolidate($enrichedData);

        } catch (\Exception $e) {
            Log::error('Error consolidating description with AI', [
                'error' => $e->getMessage(),
                'title' => $enrichedData['title'] ?? 'Unknown',
            ]);

            // Fallback em caso de erro
            return $this->fallbackConsolidator->consolidate($enrichedData);
        }
    }
}

