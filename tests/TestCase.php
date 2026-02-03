<?php

namespace Vendor\Quotes\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Vendor\Quotes\Providers\QuoteServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Configuración para testing
        config()->set('quotes.api.base_url', 'https://dummyjson.com/quotes');
        config()->set('quotes.rate_limiting.request_limit', 5);
        config()->set('quotes.rate_limiting.time_window', 30);
        config()->set('quotes.cache.ttl', 3600);
        config()->set('quotes.pagination.per_page', 10);

        // Usar cache de array para tests
        config()->set('cache.default', 'array');

        // Configuración de Laravel
        config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
        $this->app->register(\Vendor\Quotes\Providers\QuoteServiceProvider::class);
        $routesPath = __DIR__ . '/../../routes/web.php';
        if (file_exists($routesPath)) {
            require $routesPath;
        }
    }

    protected function getPackageProviders($app): array
    {
        return [
            QuoteServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        // Configuración del entorno
        $app['config']->set('app.env', 'testing');
    }
}
