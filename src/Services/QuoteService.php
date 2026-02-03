<?php

namespace Vendor\Quotes\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Vendor\Quotes\Exceptions\RateLimitExceededException;
use Vendor\Quotes\Utilities\BinarySearch;

class QuoteService
{
    protected string $cacheKey;
    protected int $cacheTtl;

    public function __construct(

        protected RateLimiter $rateLimiter,
        protected array $config = []
    ) {
        $this->config = array_merge([
            'base_url' => config('quotes.api.base_url', 'https://dummyjson.com/quotes'),
            'timeout' => config('quotes.api.timeout', 10),
        ], $config);

        $this->cacheKey = config('quotes.cache.key', 'quotes_storage');
        $this->cacheTtl = config('quotes.cache.ttl', 3600);
    }
    /**
     * Get the cache key used for storing quotes.
     */
    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }

    /**
     * Get the cache TTL.
     */
    public function getCacheTtl(): int
    {
        return $this->cacheTtl;
    }
    /**
     * Get a quote by ID using binary search on cached array.
     *
     * @throws RateLimitExceededException
     */
    public function getQuote(int $id): ?array
    {
        $cachedQuotes = Cache::get($this->cacheKey, []);

        // Use binary search to find quote

        $quote = BinarySearch::findQuoteById($cachedQuotes, $id);

        if ($quote !== null) {
            return $quote;
        }

        // If not found in cache, fetch from API
        $this->rateLimiter->attempt();
        $quote = $this->fetchFromApi($id);


        if ($quote !== null) {

            // Insert and maintain sorted order
            $cachedQuotes = BinarySearch::insertAndSort($cachedQuotes, $quote);

            // Update cache
            Cache::put($this->cacheKey, $cachedQuotes, $this->cacheTtl);

            Log::info('Quote fetched from API and cached', ['id' => $id]);
        }

        return $quote;
    }

    /**
     * Fetch a quote directly from API (bypasses cache).
     *
     * @throws RateLimitExceededException
     */
    public function fetchFromApi(int $id): ?array
    {

        try {
            $response = Http::timeout($this->config['timeout'])
                ->get($this->config['base_url'] . '/' . $id);

            if ($response->successful()) {
                return $response->json();
            }

            Log::warning('API request failed', [
                'id' => $id,
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {

            Log::error('API request exception', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Get paginated quotes from cache.
     */
    public function getPaginatedQuotes(int $page = 1, ?int $perPage = null): array
    {

        $perPage = $perPage ?: config('quotes.pagination.per_page', 10);
        $cachedQuotes = Cache::get($this->cacheKey, []);

        $total = count($cachedQuotes);
        $offset = ($page - 1) * $perPage;


        $data = array_slice($cachedQuotes, $offset, $perPage);
        // dd($data);
        return [
            'data' => $data,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => max(1, ceil($total / $perPage)),
            ],
        ];
    }

    /**
     * Get all cached quotes.
     */
    public function getAllCachedQuotes(): array
    {
        return Cache::get($this->cacheKey, []);
    }

    /**
     * Clear all cached quotes.
     */
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey);
        $this->rateLimiter->clear();
        Log::info('Quotes cache cleared');
    }

    /**
     * Get rate limiter status.
     */
    public function getRateLimitStatus(): array
    {
        return [
            'remaining' => $this->rateLimiter->getRemaining(),
            'reset_in' => $this->rateLimiter->getResetTime(),
            'limit' => $this->rateLimiter->getRequestLimit(), // ¡USA EL GETTER!
            'window' => $this->rateLimiter->getTimeWindow(),  // ¡USA EL GETTER!
        ];
    }

    /**
     * Get the rate limiter instance (para debugging).
     */
    public function getRateLimiter(): RateLimiter
    {
        return $this->rateLimiter;
    }

    /**
     * Get service configuration.
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Get cache information.
     */
    public function getCacheInfo(): array
    {
        $cachedQuotes = Cache::get($this->cacheKey, []);

        return [
            'cache_key' => $this->cacheKey,
            'cache_ttl' => $this->cacheTtl,
            'cached_count' => count($cachedQuotes),
            'cached_ids' => array_map(fn($q) => $q['id'] ?? null, $cachedQuotes),
        ];
    }
}
