<?php

namespace App\Services\BookConsolidation;

use App\Services\AI\OpenAIService;
use App\Services\AI\PromptBuilder;
use Illuminate\Support\Facades\Log;

class AISynopsisGenerator
{
    private OpenAIService $openAIService;
    private PromptBuilder $promptBuilder;

    public function __construct(
        ?OpenAIService $openAIService = null,
        ?PromptBuilder $promptBuilder = null
    ) {
        $this->openAIService = $openAIService ?? new OpenAIService();
        $this->promptBuilder = $promptBuilder ?? new PromptBuilder();
    }

    /**
     * Gera sinopse a partir da descrição completa usando IA
     * Se IA não estiver disponível, retorna versão truncada da descrição
     */
    public function generate(array $enrichedData, string $fullDescription): string
    {
        // Se IA não está habilitada, usar fallback simples
        if (!$this->openAIService->isEnabled()) {
            return $this->generateFallback($fullDescription);
        }

        try {
            $prompt = $this->promptBuilder->buildSynopsisPrompt($enrichedData);
            $cacheKey = $this->openAIService->generateCacheKey('synopsis', [
                'title' => $enrichedData['title'] ?? '',
                'sources' => $enrichedData['sources'] ?? [],
            ]);

            $synopsis = $this->openAIService->generateSynopsis($prompt, $cacheKey);

            if ($synopsis !== null && !empty(trim($synopsis))) {
                Log::info('Synopsis generated with AI', [
                    'title' => $enrichedData['title'] ?? 'Unknown',
                    'length' => strlen($synopsis),
                ]);
                return trim($synopsis);
            }

            // Fallback se IA retornar vazio
            Log::warning('AI returned empty synopsis, using fallback');
            return $this->generateFallback($fullDescription);

        } catch (\Exception $e) {
            Log::error('Error generating synopsis with AI', [
                'error' => $e->getMessage(),
                'title' => $enrichedData['title'] ?? 'Unknown',
            ]);

            // Fallback em caso de erro
            return $this->generateFallback($fullDescription);
        }
    }

    /**
     * Gera sinopse fallback truncando a descrição completa
     */
    private function generateFallback(string $fullDescription): string
    {
        if (empty($fullDescription)) {
            return 'Sinopse não disponível';
        }

        // Truncar para 500 caracteres preservando palavras
        if (strlen($fullDescription) <= 500) {
            return $fullDescription;
        }

        $truncated = substr($fullDescription, 0, 497);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }
}

