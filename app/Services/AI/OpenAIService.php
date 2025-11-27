<?php

namespace App\Services\AI;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class OpenAIService
{
    private string $apiKey;
    private string $model;
    private bool $enabled;
    private int $timeout;
    private int $maxRetries;
    private int $retryDelay;
    private bool $cacheEnabled;
    private int $cacheTtl;

    public function __construct()
    {
        $this->apiKey = config('openai.api_key', '');
        $this->model = config('openai.model', 'gpt-4o-mini');
        $this->enabled = config('openai.enabled', false);
        $this->timeout = config('openai.timeout', 30);
        $this->maxRetries = config('openai.max_retries', 3);
        $this->retryDelay = config('openai.retry_delay', 1);
        $this->cacheEnabled = config('openai.cache_enabled', true);
        $this->cacheTtl = config('openai.cache_ttl', 86400);
    }

    /**
     * Verifica se o serviço está habilitado e configurado
     */
    public function isEnabled(): bool
    {
        return $this->enabled && !empty($this->apiKey);
    }

    /**
     * Envia prompt para OpenAI e retorna texto enriquecido
     */
    public function enrichText(string $prompt, int $maxTokens, ?string $cacheKey = null): ?string
    {
        if (!$this->isEnabled()) {
            Log::debug('OpenAI service is disabled');
            return null;
        }

        // Verificar cache se habilitado
        if ($this->cacheEnabled && $cacheKey) {
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                Log::debug('OpenAI response from cache', ['cache_key' => $cacheKey]);
                return $cached;
            }
        }

        try {
            $response = $this->makeRequest($prompt, $maxTokens);

            if ($response === null) {
                return null;
            }

            // Salvar no cache se habilitado
            if ($this->cacheEnabled && $cacheKey) {
                Cache::put($cacheKey, $response, $this->cacheTtl);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Error enriching text with OpenAI', [
                'error' => $e->getMessage(),
                'prompt_length' => strlen($prompt),
            ]);
            return null;
        }
    }

    /**
     * Gera sinopse curta (máx 500 caracteres)
     */
    public function generateSynopsis(string $prompt, ?string $cacheKey = null): ?string
    {
        $maxTokens = config('openai.max_completion_tokens.synopsis', 200);
        $result = $this->enrichText($prompt, $maxTokens, $cacheKey);

        if ($result === null) {
            return null;
        }

        // Garantir que não exceda 500 caracteres
        $result = trim($result);
        if (strlen($result) > 500) {
            $result = substr($result, 0, 497) . '...';
        }

        return $result;
    }

    /**
     * Gera descrição completa
     */
    public function generateDescription(string $prompt, ?string $cacheKey = null): ?string
    {
        $maxTokens = config('openai.max_completion_tokens.description', 1000);
        return $this->enrichText($prompt, $maxTokens, $cacheKey);
    }

    /**
     * Gera biografia
     */
    public function generateBiography(string $prompt, ?string $cacheKey = null): ?string
    {
        $maxTokens = config('openai.max_completion_tokens.biography', 1000);
        return $this->enrichText($prompt, $maxTokens, $cacheKey);
    }

    /**
     * Faz requisição para API OpenAI com retry logic
     */
    private function makeRequest(string $prompt, int $maxTokens): ?string
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxRetries) {
            try {
                $response = Http::timeout($this->timeout)
                    ->withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])
                    ->post(config('openai.api_url'), [
                        'model' => $this->model,
                        'messages' => [
                            [
                                'role' => 'user',
                                'content' => $prompt,
                            ],
                            
                        ],
                    ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $content = $data['choices'][0]['message']['content'] ?? null;

                    if ($content) {
                        Log::info('OpenAI request successful', [
                            'model' => $this->model,
                            'tokens_used' => $data['usage']['total_tokens'] ?? null,
                            'attempt' => $attempt + 1,
                        ]);
                        return trim($content);
                    } else {
                        // Se response foi successful mas não tem content, não fazer retry
                        Log::warning('OpenAI response successful but no content', [
                            'response' => $data,
                            'attempt' => $attempt + 1,
                        ]);
                        return null;
                    }
                }

                // Tratar rate limit
                if ($response->status() === 429) {
                    $retryAfter = $response->header('Retry-After', $this->retryDelay * ($attempt + 1));
                    Log::warning('OpenAI rate limit hit', [
                        'retry_after' => $retryAfter,
                        'attempt' => $attempt + 1,
                    ]);
                    sleep((int)$retryAfter);
                    $attempt++;
                    continue;
                }

                // Outros erros
                Log::error('OpenAI API error', [
                    'status' => $response->status(),
                    'response' => $response->body(),
                    'attempt' => $attempt + 1,
                ]);

                if ($attempt < $this->maxRetries - 1) {
                    sleep($this->retryDelay * ($attempt + 1)); // Exponential backoff
                    $attempt++;
                    continue;
                }

                return null;

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                $lastException = $e;
                Log::warning('OpenAI connection error', [
                    'error' => $e->getMessage(),
                    'attempt' => $attempt + 1,
                ]);

                if ($attempt < $this->maxRetries - 1) {
                    sleep($this->retryDelay * ($attempt + 1));
                    $attempt++;
                    continue;
                }

            } catch (\Exception $e) {
                $lastException = $e;
                Log::error('OpenAI unexpected error', [
                    'error' => $e->getMessage(),
                    'attempt' => $attempt + 1,
                ]);
                break;
            }
        }

        if ($lastException) {
            throw $lastException;
        }

        return null;
    }

    /**
     * Gera chave de cache baseada no conteúdo
     */
    public function generateCacheKey(string $type, array $data): string
    {
        $hash = md5(json_encode($data));
        return "openai:{$type}:{$hash}";
    }
}

