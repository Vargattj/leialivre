<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações para integração com a API OpenAI para enriquecimento
    | de conteúdo com IA.
    |
    */

    'api_key' => env('OPENAI_API_KEY'),

    'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),

    'enabled' => env('OPENAI_ENABLED', false),

    'timeout' => env('OPENAI_TIMEOUT', 30),

    'max_retries' => env('OPENAI_MAX_RETRIES', 3),

    'retry_delay' => env('OPENAI_RETRY_DELAY', 1), // segundos

    /*
    |--------------------------------------------------------------------------
    | Token Limits
    |--------------------------------------------------------------------------
    |
    | Limites de tokens para diferentes tipos de conteúdo.
    |
    */

    'max_completion_tokens' => [
        'synopsis' => env('OPENAI_MAX_COMPLETION_TOKENS_SYNOPSIS', 200),
        'description' => env('OPENAI_MAX_COMPLETION_TOKENS_DESCRIPTION', 1000),
        'biography' => env('OPENAI_MAX_COMPLETION_TOKENS_BIOGRAPHY', 1000),
    ],


    /*
    |--------------------------------------------------------------------------
    | API Endpoint
    |--------------------------------------------------------------------------
    */

    'api_url' => env('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configurações de cache para evitar chamadas repetidas à API.
    |
    */

    'cache_enabled' => env('OPENAI_CACHE_ENABLED', true),

    'cache_ttl' => env('OPENAI_CACHE_TTL', 86400), // 24 horas em segundos
];

