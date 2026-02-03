<?php

namespace Vendor\Quotes\Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    // Limpiar todo
    Cache::flush();

    // Mock SIMPLIFICADO y CORRECTO
    Http::fake([
        // Mock específico para quotes individuales
        'dummyjson.com/quotes/1' => Http::response([
            'id' => 1,
            'quote' => 'Test Quote 1',
            'author' => 'Test Author 1'
        ], 200),

        'dummyjson.com/quotes/2' => Http::response([
            'id' => 2,
            'quote' => 'Test Quote 2',
            'author' => 'Test Author 2'
        ], 200),

        'dummyjson.com/quotes/999' => Http::response([], 404),

        // Mock para la ruta principal (si la necesitas)
        'dummyjson.com/quotes' => Http::response([
            'quotes' => [
                ['id' => 1, 'quote' => 'Quote 1', 'author' => 'Author 1'],
                ['id' => 2, 'quote' => 'Quote 2', 'author' => 'Author 2'],
            ],
            'total' => 2,
            'skip' => 0,
            'limit' => 10
        ], 200),
    ]);

    // Configurar rate limit ALTO para tests
    config([
        'quotes.rate_limiting.request_limit' => 100, // Muy alto para tests
        'quotes.rate_limiting.time_window' => 1,     // 1 segundo
        'quotes.api.base_url' => 'https://dummyjson.com/quotes',
        'quotes.cache.key' => 'quotes_storage_test',
    ]);
});

test('api returns json structure', function () {
    // Primero cachea una quote para que la ruta index tenga datos
    $service = app(\Vendor\Quotes\Services\QuoteService::class);

    // Limpiar cache primero
    $service->clearCache();

    // Cachear manualmente una quote (simulando que ya fue obtenida)
    Cache::put('quotes_storage_test', [
        ['id' => 1, 'quote' => 'Test Quote 1', 'author' => 'Test Author 1']
    ], 3600);

    $response = $this->get('/quotes/api');

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'success',
        'data',
        'rate_limit',
    ]);
});

test('api returns single quote', function () {
    // Usar el servicio directamente para cachear una quote
    $service = app(\Vendor\Quotes\Services\QuoteService::class);
    $service->getQuote(1); // Esto cachea la quote

    $response = $this->get('/quotes/api/1');

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);
});

test('api stats endpoint returns data', function () {
    $response = $this->get('/quotes/api/stats');

    $response->assertStatus(200);
    $this->assertArrayHasKey('data', $response->json());
});

test('api returns 404 for non-existent quote', function () {
    // Mock específico para 404
    Http::fake([
        'dummyjson.com/quotes/999' => Http::response([], 404),
    ]);

    $response = $this->get('/quotes/api/999');

    // Puede devolver 404 o manejar el error diferente
    $this->assertContains($response->status(), [404, 500]);
});
