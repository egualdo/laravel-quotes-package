<?php

namespace Vendor\Quotes\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Vendor\Quotes\Exceptions\RateLimitExceededException;
use Vendor\Quotes\Services\QuoteService;

class QuoteController extends Controller
{
    public function __construct(
        protected QuoteService $quoteService
    ) {}

    /**
     * Get paginated quotes.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
            'per_page' => 'sometimes|integer|min:1|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', config('quotes.pagination.per_page', 10));
        // $was_empty = false;
        try {

            // Primero obtenemos las citas cacheadas
            $quotes = $this->quoteService->getAllCachedQuotes();
            $was_empty = empty($quotes);
            $this->autoFetchInitialQuotes();

            // Volver a obtener las citas después del auto-fetch
            $quotes = $this->quoteService->getAllCachedQuotes();

            return response()->json([
                'success' => true,
                'data' => $quotes,
                'rate_limit' => $this->quoteService->getRateLimitStatus(),
                'was_empty' => $was_empty,
                'total_cached' => count($quotes) ?? 0
            ]);
        } catch (RateLimitExceededException $e) {
            return response()->json([
                'error' => 'rate_limit_exceeded',
                'message' => $e->getMessage(),
                'retry_after' => $e->getCode() === 429 ? 30 : null,
            ], 429);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'internal_error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Fetch and cache initial quotes if cache is empty.
     */
    private function autoFetchInitialQuotes(int $maxQuotes = 5): void
    {
        $cachedQuotes = $this->quoteService->getAllCachedQuotes();

        if (empty($cachedQuotes)) {
            // Si no hay caché, buscar desde el ID 1
            $this->fetchNewQuotesFromStart($maxQuotes);
        } else {
            // Si ya hay caché, buscar partiendo del último ID
            $this->fetchNewQuotesFromLastId($maxQuotes, $cachedQuotes);
        }
    }

    private function fetchNewQuotesFromStart(int $maxQuotes): void
    {
        try {
            $ids = range(1, $maxQuotes);
            $this->fetchAndCacheQuotes($ids);
        } catch (\Exception $e) {
            Log::error('Auto-fetch from start failed', [
                'error' => $e->getMessage(),
                'max_quotes' => $maxQuotes
            ]);
        }
    }

    /**
     * Buscar nuevas citas partiendo del último ID en caché
     */
    private function fetchNewQuotesFromLastId(int $maxQuotes, array $cachedQuotes): void
    {
        try {
            // Obtener el último ID de las citas en caché
            $lastQuote = end($cachedQuotes);

            $lastId = $lastQuote['id'] ?? 0;

            if ($lastId > 0) {
                // Crear IDs a partir del último ID + 1
                $startId = $lastId + 1;
                // Limitar a máximo 10 IDs
                $endId = $startId + $maxQuotes - 1;
                $ids = range($startId, $endId);

                Log::info('Fetching new quotes starting from last cached ID', [
                    'last_cached_id' => $lastId,
                    'start_id' => $startId,
                    'end_id' => $endId,
                    'count' => count($ids)
                ]);

                $this->fetchAndCacheQuotes($ids);
            }
        } catch (\Exception $e) {
            Log::error('Auto-fetch from last ID failed', [
                'error' => $e->getMessage(),
                'max_quotes' => $maxQuotes
            ]);
        }
    }

    private function fetchAndCacheQuotes(array $ids): void
    {
        $successfulFetches = 0;

        foreach ($ids as $id) {
            try {
                // Intenta obtener la cita (esto la cacheará automáticamente si existe)
                $quote = $this->quoteService->getQuote($id);

                if ($quote !== null) {
                    $successfulFetches++;
                } else {
                    // Si no existe la cita, puede que sea un ID inválido
                    Log::warning('Quote not found during auto-fetch', ['id' => $id]);
                    // Continuar con el siguiente ID
                }
            } catch (RateLimitExceededException $e) {
                Log::warning('Rate limit exceeded during auto-fetch', [
                    'id' => $id,
                    'successful_fetches' => $successfulFetches
                ]);

                break;
            } catch (\Exception $e) {
                Log::warning('Failed to fetch quote during auto-fetch', [
                    'id' => $id,
                    'error' => $e->getMessage()
                ]);

                continue;
            }
        }

        Log::info('Auto-fetch completed', [
            'requested_count' => count($ids),
            'successful_fetches' => $successfulFetches
        ]);
    }


    /**
     * Get a single quote by ID.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'Invalid quote ID',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $quote = $this->quoteService->getQuote($id);

            if ($quote === null) {
                return response()->json([
                    'error' => 'not_found',
                    'message' => "Quote with ID {$id} not found in API",
                    'suggestion' => 'Try a different ID or use batch import first',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $quote,
                'rate_limit' => $this->quoteService->getRateLimitStatus(),
                'cache_info' => [
                    'source' => 'cache_or_api',
                    'cached_at' => now()->toIso8601String(),
                ],
            ]);
        } catch (RateLimitExceededException $e) {
            return response()->json([
                'error' => 'rate_limit_exceeded',
                'message' => $e->getMessage(),
                'retry_after' => 30,
                'suggestion' => 'Wait and try again or use batch import command',
            ], 429);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'internal_error',
                'message' => 'Failed to fetch quote: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get cache and rate limit statistics.
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $cachedQuotes = $this->quoteService->getAllCachedQuotes();

        return response()->json([
            'success' => true,
            'data' => [
                'cache' => $this->quoteService->getCacheInfo(),
                'rate_limit' => $this->quoteService->getRateLimitStatus(),
                'cached_quotes_count' => count($cachedQuotes),
                'cached_ids' => array_column($cachedQuotes, 'id'),
                'suggestions' => empty($cachedQuotes) ? [
                    'Use /quotes/api',
                    'Run: php artisan quotes:batch-import 10',
                    'Fetch individual: /quotes/api/1',
                ] : null,
            ],
        ]);
    }

    /**
     * Clear the quotes cache.
     *
     * @return JsonResponse
     */
    public function clearCache(): JsonResponse
    {
        $previousCount = count($this->quoteService->getAllCachedQuotes());
        $this->quoteService->clearCache();

        return response()->json([
            'success' => true,
            'message' => 'Quotes cache cleared successfully',
            'data' => [
                'previous_count' => $previousCount,
                'current' => $this->quoteService->getCacheInfo(),
            ],
        ]);
    }
}
