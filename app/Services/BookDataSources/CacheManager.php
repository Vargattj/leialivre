<?php

namespace App\Services\BookDataSources;

use Illuminate\Support\Facades\Cache;

class CacheManager
{
    public function get(string $key)
    {
        return Cache::store('file')->get($key);
    }

    public function put(string $key, $value, int $ttl): bool
    {
        try {
            return Cache::store('file')->put($key, $value, $ttl);
        } catch (\Throwable $t) {
            return false;
        }
    }

    public function remember(string $key, int $ttl, callable $callback)
    {
        $cached = $this->get($key);
        if ($cached !== null) {
            return $cached;
        }

        $value = $callback();
        if ($value !== null) {
            $this->put($key, $value, $ttl);
        }

        return $value;
    }
}

