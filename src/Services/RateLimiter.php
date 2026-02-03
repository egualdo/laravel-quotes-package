<?php

namespace Vendor\Quotes\Services;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Vendor\Quotes\Exceptions\RateLimitExceededException;

class RateLimiter
{
    public function __construct(
        protected CacheRepository $cache,
        protected int $requestLimit = 5,
        protected int $timeWindow = 30,
        protected string $cacheKey = 'quotes_rate_limit'
    ) {}

    /**
     * Attempt to make a request, throwing if rate limit is exceeded.
     *
     * @throws RateLimitExceededException
     */
    public function attempt(): void
    {
        $hits = $this->cache->get($this->cacheKey, []);
        $now = time();

        // Filter hits within the current time window
        $hits = array_filter($hits, function (int $timestamp) use ($now): bool {
            return $timestamp > $now - $this->timeWindow;
        });

        if (count($hits) >= $this->requestLimit) {
            $resetTime = $this->getResetTime();
            throw new RateLimitExceededException(
                sprintf(
                    'Rate limit exceeded. %d requests per %d seconds allowed. Reset in %d seconds.',
                    $this->requestLimit,
                    $this->timeWindow,
                    $resetTime
                )
            );
        }

        $hits[] = $now;
        $this->cache->put($this->cacheKey, $hits, $this->timeWindow);
    }

    /**
     * Get remaining requests in the current window.
     */
    public function getRemaining(): int
    {
        $hits = $this->cache->get($this->cacheKey, []);
        $now = time();

        $hits = array_filter($hits, function (int $timestamp) use ($now): bool {
            return $timestamp > $now - $this->timeWindow;
        });

        return max(0, $this->requestLimit - count($hits));
    }

    /**
     * Get time until rate limit resets (in seconds).
     */
    public function getResetTime(): int
    {
        $hits = $this->cache->get($this->cacheKey, []);

        if (empty($hits)) {
            return 0;
        }

        $oldest = min($hits);
        $reset = ($oldest + $this->timeWindow) - time();

        return max(0, $reset);
    }

    /**
     * Clear the rate limit counter.
     */
    public function clear(): void
    {
        $this->cache->forget($this->cacheKey);
    }
    /**
     * Get the request limit value.
     */
    public function getRequestLimit(): int
    {
        return $this->requestLimit;
    }

    /**
     * Get the time window value.
     */
    public function getTimeWindow(): int
    {
        return $this->timeWindow;
    }

    /**
     * Get the cache key.
     */
    public function getCacheKey(): string
    {
        return $this->cacheKey;
    }
}
