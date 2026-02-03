<?php

namespace Vendor\Quotes\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Sleep;
use Vendor\Quotes\Exceptions\RateLimitExceededException;
use Vendor\Quotes\Services\QuoteService;
use Vendor\Quotes\Utilities\BinarySearch;

class BatchImportQuotesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotes:batch-import 
                            {count : Number of unique quotes to fetch}
                            {--start=1 : Starting ID for fetching}
                            {--max-attempts=100 : Maximum API attempts before giving up}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch unique quotes with automatic rate limit handling and uniqueness validation';

    /**
     * Quote service instance.
     */
    protected QuoteService $quoteService;

    /**
     * Collected unique quote IDs during this run.
     */
    protected Collection $collectedIds;

    /**
     * Currently cached quote IDs.
     */
    protected array $cachedIds = [];

    /**
     * Statistics for the command execution.
     */
    protected array $stats = [
        'fetched' => 0,
        'duplicates_skipped' => 0,
        'rate_limit_hits' => 0,
        'errors' => 0,
        'total_attempts' => 0,
    ];

    /**
     * Create a new command instance.
     */
    public function __construct(QuoteService $quoteService)
    {
        parent::__construct();

        $this->quoteService = $quoteService;
        $this->collectedIds = new Collection();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = (int) $this->argument('count');
        $startId = (int) $this->option('start');
        $maxAttempts = (int) $this->option('max-attempts');

        // Validate input
        if ($count <= 0) {
            $this->error('Count must be greater than 0.');
            return Command::FAILURE;
        }

        if ($count > 100) {
            if (!$this->confirm("You're about to fetch {$count} quotes. This may take a while. Continue?")) {
                $this->info('Operation cancelled.');
                return Command::SUCCESS;
            }
        }

        // Load currently cached IDs
        $this->loadCachedIds();

        $this->info("Starting batch import of {$count} unique quotes...");
        $this->info("Starting from ID: {$startId}");
        $this->info("Rate limit: {$this->getRateLimitInfo()}");
        $this->newLine();

        // Create progress bar
        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        $currentId = $startId;
        $attempts = 0;

        while ($this->stats['fetched'] < $count && $attempts < $maxAttempts) {
            $attempts++;
            $this->stats['total_attempts']++;

            try {
                $result = $this->fetchAndProcessQuote($currentId);

                if ($result['success']) {
                    $this->stats['fetched']++;
                    $progressBar->advance();

                    $this->collectedIds->push($result['id']);
                    $this->cachedIds[] = $result['id'];

                    $this->displayProgressUpdate();
                } else {
                    $this->stats['duplicates_skipped']++;
                }

                $currentId++;
            } catch (RateLimitExceededException $e) {
                $this->handleRateLimitExceeded();
                continue; // Retry same ID after waiting

            } catch (\Exception $e) {
                $this->stats['errors']++;
                $this->warn("Error fetching ID {$currentId}: " . $e->getMessage());
                $currentId++;
                continue;
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->displayResults($count);

        if ($this->stats['fetched'] < $count) {
            $this->warn("Only fetched {$this->stats['fetched']} out of {$count} requested quotes.");
            $this->warn("Maximum attempts ({$maxAttempts}) reached.");
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Fetch and process a single quote.
     */
    protected function fetchAndProcessQuote(int $id): array
    {
        // Check if ID is already cached or collected
        if ($this->isDuplicateId($id)) {
            return [
                'success' => false,
                'id' => $id,
                'reason' => 'duplicate'
            ];
        }

        // Fetch quote from API (will handle rate limiting internally)
        $quote = $this->quoteService->fetchFromApi($id);

        if ($quote === null) {
            return [
                'success' => false,
                'id' => $id,
                'reason' => 'not_found'
            ];
        }

        // Double-check uniqueness with the actual fetched quote ID
        if ($this->isDuplicateId($quote['id'])) {
            return [
                'success' => false,
                'id' => $quote['id'],
                'reason' => 'duplicate_after_fetch'
            ];
        }

        // Store in cache using the service (which will use binary search insertion)
        $this->quoteService->getQuote($quote['id']);

        return [
            'success' => true,
            'id' => $quote['id'],
            'quote' => $quote
        ];
    }

    /**
     * Check if an ID is already cached or collected.
     */
    protected function isDuplicateId(int $id): bool
    {
        // Usa el nuevo método optimizado
        return BinarySearch::idExists($this->cachedIds, $id) ||
            $this->collectedIds->contains($id);
    }

    /**
     * Handle rate limit exceeded exception.
     */
    protected function handleRateLimitExceeded(): void
    {
        $this->stats['rate_limit_hits']++;

        $status = $this->quoteService->getRateLimitStatus();
        $waitTime = $status['reset_in'] ?: 30;

        $this->warn("Rate limit hit! Waiting {$waitTime} seconds...");
        $this->displayRateLimitStatus($status);

        // Sleep for the reset time + 1 second buffer
        Sleep::for($waitTime + 1)->seconds();

        $this->info("Resuming operations...");
    }

    /**
     * Load currently cached quote IDs.
     */
    protected function loadCachedIds(): void
    {
        try {
            // Intenta obtener los quotes cacheados
            $cachedQuotes = $this->quoteService->getAllCachedQuotes();
            $this->cachedIds = array_column($cachedQuotes, 'id');

            // Ordenar para binary search
            sort($this->cachedIds);

            if (!empty($this->cachedIds)) {
                $this->info("Found " . count($this->cachedIds) . " previously cached quotes.");
                $this->line("Cached IDs: " . implode(', ', $this->cachedIds), verbosity: 'verbose');
            } else {
                $this->info("No quotes found in cache.");
            }
        } catch (\Exception $e) {
            $this->warn("Could not load cached quotes: " . $e->getMessage());
            $this->cachedIds = [];
        }
    }

    /**
     * Get rate limit information string.
     */
    protected function getRateLimitInfo(): string
    {
        $status = $this->quoteService->getRateLimitStatus();
        return "{$status['limit']} requests per {$status['window']} seconds";
    }

    /**
     * Display current progress update.
     */
    protected function displayProgressUpdate(): void
    {
        $fetched = $this->stats['fetched'];
        $count = (int) $this->argument('count');
        $duplicates = $this->stats['duplicates_skipped'];
        $rateLimitHits = $this->stats['rate_limit_hits'];

        $this->line(
            "Fetched: <info>{$fetched}/{$count}</info> | " .
                "Duplicates skipped: <comment>{$duplicates}</comment> | " .
                "Rate limits hit: <comment>{$rateLimitHits}</comment>",
            verbosity: 'verbose'
        );
    }

    /**
     * Display rate limit status.
     */
    protected function displayRateLimitStatus(array $status): void
    {
        $this->line(
            "Rate limit status: " .
                "<info>{$status['remaining']}</info>/<comment>{$status['limit']}</comment> remaining, " .
                "resets in <comment>{$status['reset_in']}</comment> seconds",
            verbosity: 'verbose'
        );
    }

    /**
     * Display final results.
     */
    protected function displayResults(int $requestedCount): void
    {
        $this->info('=== BATCH IMPORT COMPLETE ===');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Requested quotes', $requestedCount],
                ['Successfully fetched', $this->stats['fetched']],
                ['Duplicates skipped', $this->stats['duplicates_skipped']],
                ['Rate limit hits', $this->stats['rate_limit_hits']],
                ['Errors encountered', $this->stats['errors']],
                ['Total API attempts', $this->stats['total_attempts']],
                ['Efficiency rate', round(($this->stats['fetched'] / $this->stats['total_attempts']) * 100, 1) . '%'],
            ]
        );

        if ($this->collectedIds->isNotEmpty()) {
            $this->info('Newly imported quote IDs:');
            $this->line(implode(', ', $this->collectedIds->sort()->values()->all()));
        }

        $totalCached = count($this->cachedIds);
        $this->info("Total quotes now in cache: {$totalCached}");

        $status = $this->quoteService->getRateLimitStatus();
        $this->info("Final rate limit: {$status['remaining']}/{$status['limit']} remaining");
    }
}
