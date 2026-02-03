<?php

use Illuminate\Support\Facades\Http;

beforeEach(function () {
    \Illuminate\Support\Facades\Cache::flush();

    // Mock simple y directo
    Http::fake([
        'dummyjson.com/quotes/1' => Http::response(['id' => 1], 200),
        'dummyjson.com/quotes/2' => Http::response(['id' => 2], 200),
        'dummyjson.com/quotes/3' => Http::response(['id' => 3], 200),
    ]);
});

test('batch import command fetches specified number of quotes', function () {
    $this->artisan('quotes:batch-import', ['count' => 2])
        ->expectsOutput('Starting batch import of 2 unique quotes...')
        ->assertExitCode(0);
});

test('batch import command handles rate limits automatically', function () {
    // Configurar rate limit muy bajo
    config(['quotes.rate_limiting.request_limit' => 5]);
    config(['quotes.rate_limiting.time_window' => 30]);
    // 30 segundos para que el test no tarde mucho

    $this->artisan('quotes:batch-import', ['count' => 2])
        ->expectsOutput('Rate limit: 5 requests per 30 seconds')
        ->assertExitCode(0);
});

test('batch import command ensures uniqueness', function () {
    // Primero importar una quote
    $this->artisan('quotes:batch-import', ['count' => 1, '--start' => 1])
        ->assertExitCode(0);

    // Intentar importar otra vez (debería saltar duplicados)
    $this->artisan('quotes:batch-import', ['count' => 2, '--start' => 1])
        ->assertExitCode(0);
});

test('batch import command validates input', function () {
    $this->artisan('quotes:batch-import', ['count' => 0])
        ->expectsOutput('Count must be greater than 0.')
        ->assertExitCode(1);
});
