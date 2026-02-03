# Laravel Quotes Package

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-007ACC?style=for-the-badge&logo=typescript&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)

**Laravel Package for getting, caching and show quotes with rate limiting and UI Vue.js**

</div>

# 🚀 Installing

### 1. previous requirements

- PHP 8.1+
- Laravel 10+
- Composer
- Extensión PHP cURL

### 🚀 Quick start

# ⚠️ IMPORTANT: Before start

**Docker must be installed and running before continue.**

### Automatic script

```bash
# 1. Clone the repository
git clone https://github.com/egualdo/laravel-quotes-package.git
cd laravel-quotes-package

# 2. Execute the command
docker-compose up -d

# 3. Access to:
# http://localhost:8080/quotes/ui
```

### Commands to start:

```bash
# Import initial quotes
docker-compose exec app php artisan quotes:batch-import 10

# System stats
curl http://localhost:8080/quotes/api/stats

# Execute tests
docker-compose exec app ./vendor/bin/pest
```

## ⚡ Rate Limiting Estrategy

### 📊 Overview

The package implements a persistent rate limiting system that respects the limits of the external API (dummyjson.com), preventing blockages and ensuring responsible use.

### 🎯 Main Features

### 1. Persistence Between Requests

```bash
$this->cache->put($this->cacheKey, $hits, $this->timeWindow);
```

- Counters persist across different requests

- Compatible with drivers: redis, database, file, array

- State is not lost upon application restart (depending on the driver)

### 2. Without Sleep/Wait

```bash
public function attempt(): void
{
    if (count($hits) >= $this->requestLimit) {
        throw new RateLimitExceededException(
            "Rate limit exceeded. {$this->requestLimit} requests per " .
            "{$this->timeWindow} seconds allowed."
        );
    }
}
```

### 3. Flexible Configuration

```bash
// Config by .env or config/quotes.php
'rate_limiting' => [
    'request_limit' => env('QUOTES_REQUEST_LIMIT', 5),  // Requests
    'time_window' => env('QUOTES_TIME_WINDOW', 30),     // Seconds
],
```

### 🔄 How it work

### Step 1: Application Registration

```bash
// Each success request register a timestamp
$hits[] = time();
$this->cache->put($this->cacheKey, $hits, $this->timeWindow);
```

### Step 2: Filtering by Temporary Window

```bash
// It only counts requests within the time window
$hits = array_filter($hits, function (int $timestamp) use ($now): bool {
    return $timestamp > $now - $this->timeWindow;
});
```

### Step 3: Limit Verification

```bash
// Check if the limit was exceeded
if (count($hits) >= $this->requestLimit) {
    // Calculate remaining time
    $resetTime = $this->getResetTime();
    throw new RateLimitExceededException($resetTime);
}
```

### Step 4: Reset Time Calculation

```bash
public function getResetTime(): int
{
    $hits = $this->cache->get($this->cacheKey, []);
    if (empty($hits)) return 0;

    $oldest = min($hits);
    $reset = ($oldest + $this->timeWindow) - time();
    return max(0, $reset);
}
```

### 🛡️ Handling Exeptions

### In the service

```bash
try {
    $this->rateLimiter->attempt();
    // Calling external API...
} catch (RateLimitExceededException $e) {
    // Specific handling rate limiting
    throw $e;
}
```

### In the Command (Automatic Retry)

```bash
// BatchImportCommand.php handle exception automatically
try {
    $quote = $this->quoteService->fetchFromApi($id);
} catch (RateLimitExceededException $e) {
    $this->handleRateLimitExceeded();
    continue; // Retry after waiting
}
```

### In the API (HTTP Response)

```bash
// QuoteController returns HTTP 429
catch (RateLimitExceededException $e) {
    return response()->json([
        'error' => 'rate_limit_exceeded',
        'message' => $e->getMessage(),
        'retry_after' => 30,
    ], 429);
}
```

### 📈 Metrics and Monitoring

### Status Available

```bash
$status = $quoteService->getRateLimitStatus();
// returns:
[
    'remaining' => 3,      // Remaining Request
    'reset_in' => 15,      // Seconds until resets
    'limit' => 5,          // Total Limit
    'window' => 30         // Windows in seconds
]
```

### stats Endpoints

```bash
GET /quotes/api/stats
```

### 🚀 Advantages of This Implementation

#### External API Respectful: Prevents blocks due to abuse

#### Persistent: Maintains state between requests

#### Configurable: Adapts to different limits

#### Block-Free: Does not put the PHP process to sleep

#### Informative: Provides metrics and reset times

#### Integrated: Works with the Laravel ecosystem

### 🐳 Instructions for Running the Docker Environment

### 📋 Prerequisites

#### Docker 20.10+ installed

#### Docker Compose 2.0+

#### 2GB of available RAM

#### Available ports 8080 and 3000

### 🛠️ Available Services

<table>
<thead>
<tr>
<td>Services</td>
<td>Ports</td>
<td>Description</td>
<td>Access</td>
</tr>			         
<thead>
<tbody><tr><td><span>app</span></td><td><span>9000</span></td><td><span>Aplication Laravel</span></td><td><span>Intern</span></td></tr><tr><td><span>nginx</span></td><td><span>8080</span></td><td><span>Web Server</span></td><td><a href="http://localhost:8080" target="_blank" rel="noreferrer"><span>http://localhost:8080</span></a></td></tr><tr><td><span>mysql</span></td><td><span>3306</span></td><td><span>Database</span></td><td><span>localhost:3306</span></td></tr><tr><td><span>redis</span></td><td><span>6379</span></td><td><span>Cache Redis</span></td><td><span>localhost:6379</span></td></tr></tbody>
</table>

### 🎯 Access Summary

<table><thead><tr><th><span>Resource</span></th><th><span>URL</span></th><th><span>Credentials</span></th></tr></thead><tbody><tr><td><span>Application</span></td><td><a href="http://localhost:8080" target="_blank" rel="noreferrer"><span>http://localhost:8080</span></a></td><td><span>-</span></td></tr><tr><td><span>UI Quotes</span></td><td><a href="http://localhost:8080/quotes/ui" target="_blank" rel="noreferrer"><span>http://localhost:8080/quotes/ui</span></a></td><td><span>-</span></td></tr><tr><td><span>API Quotes</span></td><td><a href="http://localhost:8080/quotes/api" target="_blank" rel="noreferrer"><span>http://localhost:8080/quotes/api</span></a></td><td><span>-</span></td></tr><tr><td><span>PHPMyAdmin</span></td><td><a href="http://localhost:8081" target="_blank" rel="noreferrer"><span>http://localhost:8081</span></a></td><td><span>root/secret</span></td></tr><tr><td><span>Redis Commander</span></td><td><a href="http://localhost:8082" target="_blank" rel="noreferrer"><span>http://localhost:8082</span></a></td><td><span>-</span></td></tr></tbody></table>
