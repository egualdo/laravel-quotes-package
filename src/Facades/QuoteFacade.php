<?php

namespace Vendor\Quotes\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Vendor\Quotes\Services\QuoteService getQuote(int $id)
 * @method static array|null fetchFromApi(int $id)
 * @method static array getPaginatedQuotes(int $page = 1, ?int $perPage = null)
 * @method static array getAllCachedQuotes()
 * @method static void clearCache()
 * @method static array getRateLimitStatus()
 *
 * @see \Vendor\Quotes\Services\QuoteService
 */
class QuoteFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Vendor\Quotes\Services\QuoteService::class;
    }
}
