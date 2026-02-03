<?php

namespace Vendor\Quotes\Providers;

use Illuminate\Support\ServiceProvider;
use Vendor\Quotes\Services\RateLimiter;
use Vendor\Quotes\Services\QuoteService;

class QuoteServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $configPath = __DIR__ . '/../../config/quotes.php';
        $this->mergeConfigFrom($configPath, 'quotes');

        // Registrar RateLimiter con parámetros explícitos
        $this->app->singleton(RateLimiter::class, function ($app) {
            return new RateLimiter(
                $app['cache']->store(),
                (int) config('quotes.rate_limiting.request_limit', 5),
                (int) config('quotes.rate_limiting.time_window', 30),
                'quotes_rate_limit'
            );
        });

        // Registrar QuoteService
        $this->app->singleton(QuoteService::class, function ($app) {
            return new QuoteService(
                $app->make(RateLimiter::class), // RateLimiter inyectado
                config('quotes.api', []) // Configuración
            );
        });

        // Registrar alias para Facade
        $this->app->alias(QuoteService::class, 'quotes.service');
    }

    public function boot(): void
    {
        // Cargar vistas
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'quotes');

        // Cargar rutas
        $routesPath = __DIR__ . '/../../routes/web.php';
        if (file_exists($routesPath)) {
            $this->loadRoutesFrom($routesPath);
        }

        if ($this->app->runningInConsole()) {
            // Publicar configuración
            $this->publishes([
                __DIR__ . '/../../config/quotes.php' => config_path('quotes.php'),
            ], 'quotes-config');

            // Publicar vistas
            $this->publishes([
                __DIR__ . '/../../resources/views' => resource_path('views/vendor/quotes'),
            ], 'quotes-views');
        }
        // Opcional: Registrar comandos
        $this->commands([
            \Vendor\Quotes\Console\Commands\BatchImportQuotesCommand::class,
        ]);
    }
}
