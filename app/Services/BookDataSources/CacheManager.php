<?php

namespace App\Services\BookDataSources;

use Illuminate\Support\Facades\Cache;

class CacheManager
{
    /**
     * Cache desativado - sempre retorna null
     */
    public function get(string $key)
    {
        return null;
    }

    /**
     * Cache desativado - não faz nada
     */
    public function put(string $key, $value, int $ttl): bool
    {
        return false;
    }

    /**
     * Cache desativado - sempre executa o callback sem verificar cache
     */
    public function remember(string $key, int $ttl, callable $callback)
    {
        // Sempre executa o callback sem verificar cache
        return $callback();
    }
}

