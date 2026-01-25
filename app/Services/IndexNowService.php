<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IndexNowService
{
    protected string $key;
    protected string $host;
    protected string $keyLocation;
    protected string $apiEndpoint = 'https://www.bing.com/indexnow';

    public function __construct()
    {
        $this->key = config('services.indexnow.key') ?? env('INDEXNOW_KEY');
        $this->host = parse_url(config('app.url'), PHP_URL_HOST);
        $this->keyLocation = config('app.url') . '/' . $this->key . '.txt';
    }

    /**
     * Submit a single URL or an array of URLs to IndexNow.
     *
     * @param string|array $urls
     * @return bool
     */
    public function submit($urls): bool
    {
        if (empty($this->key)) {
            Log::warning('IndexNow: Key not configured.');
            return false;
        }

        $urlList = is_array($urls) ? $urls : [$urls];

        // Ensure URLs are full URLs
        $urlList = array_map(function ($url) {
            if (!str_starts_with($url, 'http')) {
                return config('app.url') . (str_starts_with($url, '/') ? '' : '/') . $url;
            }
            return $url;
        }, $urlList);

        try {
            $response = Http::post($this->apiEndpoint, [
                'host' => $this->host,
                'key' => $this->key,
                'keyLocation' => $this->keyLocation,
                'urlList' => $urlList,
            ]);

            if ($response->successful()) {
                Log::info('IndexNow: URLs submitted successfully.', ['urls' => $urlList]);
                return true;
            }

            Log::error('IndexNow: Submission failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
                'urls' => $urlList
            ]);
        } catch (\Exception $e) {
            Log::error('IndexNow: Exception during submission.', [
                'message' => $e->getMessage(),
                'urls' => $urlList
            ]);
        }

        return false;
    }
}
