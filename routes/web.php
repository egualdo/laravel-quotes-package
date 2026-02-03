<?php

use Illuminate\Support\Facades\Route;
use Vendor\Quotes\Http\Controllers\QuoteController;

Route::prefix('quotes')->name('quotes.')->group(function () {
    // UI Vue.js
    Route::get('/ui', function () {
        return view('quotes::ui');
    })->name('ui');

    // API Routes
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/', [QuoteController::class, 'index'])->name('index');
        Route::get('/stats', [QuoteController::class, 'stats'])->name('stats');
        Route::delete('/cache', [QuoteController::class, 'clearCache'])->name('clear-cache');
        Route::get('/{id}', [QuoteController::class, 'show'])->name('show');
    });
});
