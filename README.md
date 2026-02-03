# Laravel Quotes Package

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-007ACC?style=for-the-badge&logo=typescript&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)

**Paquete Laravel para obtener, cachear y mostrar citas con rate limiting y UI Vue.js**

</div>

## 📋 Tabla de Contenidos

- [🚀 Instalación](#instalación)
- [📖 Uso](#uso)
- [🔧 Configuración](#configuración)
- [🌐 API](#api)
- [💻 Interfaz Web](#interfaz-web)
- [⚙️ Comandos](#comandos)
- [🧪 Testing](#testing)
- [🐳 Docker](#docker)

## 🚀 Instalación

### 1. Requisitos previos

- PHP 8.1+
- Laravel 10+
- Composer
- Extensión PHP cURL

### 2. Instalar el paquete

```bash
composer require vendor/quotes
```

## ⚡ Estrategia de Rate Limiting

### 📊 Visión General

El paquete implementa un sistema de rate limiting persistente que respeta los límites de la API externa (dummyjson.com), evitando bloqueos y garantizando un uso responsable.

### 🎯 Características Principales

### 1. Persistencia Entre Requests

```bash
$this->cache->put($this->cacheKey, $hits, $this->timeWindow);
```

- Los contadores persisten entre diferentes solicitudes

- Compatible con drivers: redis, database, file, array

- No se pierde el estado al reiniciar la aplicación (dependiendo del driver)

### 2. Sin Sleep/Wait

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

### 3. Configuración Flexible

```bash
// Configurable via .env o config/quotes.php
'rate_limiting' => [
    'request_limit' => env('QUOTES_REQUEST_LIMIT', 5),  // Solicitudes
    'time_window' => env('QUOTES_TIME_WINDOW', 30),     // Segundos
],
```

### 🔄 Cómo Funciona

### Paso 1: Registro de Solicitud

```bash
// Cada solicitud exitosa registra un timestamp
$hits[] = time();
$this->cache->put($this->cacheKey, $hits, $this->timeWindow);
```

### Paso 2: Filtrado por Ventana Temporal

```bash
// Solo cuenta solicitudes dentro de la ventana de tiempo
$hits = array_filter($hits, function (int $timestamp) use ($now): bool {
    return $timestamp > $now - $this->timeWindow;
});
```

### Paso 3: Verificación de Límite

```bash
// Verifica si se excedió el límite
if (count($hits) >= $this->requestLimit) {
    // Calcula tiempo restante
    $resetTime = $this->getResetTime();
    throw new RateLimitExceededException($resetTime);
}
```

### Paso 4: Cálculo de Tiempo de Reset

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

### 🛡️ Manejo de Excepciones

### En el Servicio

```bash
try {
    $this->rateLimiter->attempt();
    // Llamada a API externa...
} catch (RateLimitExceededException $e) {
    // Manejo específico para rate limiting
    throw $e;
}
```

### En el Comando (Reintento Automático)

```bash
// BatchImportCommand.php maneja la excepción automáticamente
try {
    $quote = $this->quoteService->fetchFromApi($id);
} catch (RateLimitExceededException $e) {
    $this->handleRateLimitExceeded();
    continue; // Reintenta después de esperar
}
```

### En la API (Respuesta HTTP)

```bash
// QuoteController devuelve HTTP 429
catch (RateLimitExceededException $e) {
    return response()->json([
        'error' => 'rate_limit_exceeded',
        'message' => $e->getMessage(),
        'retry_after' => 30,
    ], 429);
}
```

### 📈 Métricas y Monitoreo

### Estado Disponible

```bash
$status = $quoteService->getRateLimitStatus();
// Devuelve:
[
    'remaining' => 3,      // Solicitudes restantes
    'reset_in' => 15,      // Segundos hasta reset
    'limit' => 5,          // Límite total
    'window' => 30         // Ventana en segundos
]
```

### Endpoint de Estadísticas

```bash
GET /quotes/api/stats
```

### 🚀 Ventajas de Esta Implementación

### Respetuoso con API Externa: Previene bloqueos por abuso

#### Persistente: Mantiene estado entre requests

#### Configurable: Se adapta a diferentes límites

#### Sin Bloqueos: No duerme el proceso PHP

#### Informativo: Proporciona métricas y tiempos de reset

#### Integrado: Funciona con el ecosistema Laravel

### 🐳 Instrucciones para Ejecutar el Entorno Docker

### 📋 Requisitos Previos

#### Docker 20.10+ instalado

#### Docker Compose 2.0+

#### 2GB de RAM disponible

#### Puertos 8080 y 3000 disponibles

### 🚀 Inicio Rápido

### Opción 1: Script Automático (Recomendado)

```bash
# 1. Clonar el repositorio
git clone <tu-repositorio>
cd quotes-package

# 2. Ejecutar script de instalación
chmod +x docker-setup.sh
./docker-setup.sh

# 3. Acceder a la aplicación
# Abre: http://localhost:8080/quotes/ui
```

### 🛠️ Servicios Disponibles

<table>
<thead>
<tr>
<td>Servicio</td>
<td>Puerto</td>
<td>Descripción</td>
<td>Acceso</td>
</tr>			         
<thead>
<tbody><tr><td><span>app</span></td><td><span>9000</span></td><td><span>Aplicación Laravel</span></td><td><span>Interno</span></td></tr><tr><td><span>nginx</span></td><td><span>8080</span></td><td><span>Servidor Web</span></td><td><a href="http://localhost:8080" target="_blank" rel="noreferrer"><span>http://localhost:8080</span></a></td></tr><tr><td><span>mysql</span></td><td><span>3306</span></td><td><span>Base de datos</span></td><td><span>localhost:3306</span></td></tr><tr><td><span>redis</span></td><td><span>6379</span></td><td><span>Cache Redis</span></td><td><span>localhost:6379</span></td></tr></tbody>
</table>

### 🎯 Resumen de Acceso

<table><thead><tr><th><span>Recurso</span></th><th><span>URL</span></th><th><span>Credenciales</span></th></tr></thead><tbody><tr><td><span>Aplicación</span></td><td><a href="http://localhost:8080" target="_blank" rel="noreferrer"><span>http://localhost:8080</span></a></td><td><span>-</span></td></tr><tr><td><span>UI Quotes</span></td><td><a href="http://localhost:8080/quotes/ui" target="_blank" rel="noreferrer"><span>http://localhost:8080/quotes/ui</span></a></td><td><span>-</span></td></tr><tr><td><span>API Quotes</span></td><td><a href="http://localhost:8080/quotes/api" target="_blank" rel="noreferrer"><span>http://localhost:8080/quotes/api</span></a></td><td><span>-</span></td></tr><tr><td><span>PHPMyAdmin</span></td><td><a href="http://localhost:8081" target="_blank" rel="noreferrer"><span>http://localhost:8081</span></a></td><td><span>root/secret</span></td></tr><tr><td><span>Redis Commander</span></td><td><a href="http://localhost:8082" target="_blank" rel="noreferrer"><span>http://localhost:8082</span></a></td><td><span>-</span></td></tr></tbody></table>

### Comandos útiles para empezar:

```bash
# Importar citas de ejemplo
docker-compose exec app php artisan quotes:batch-import 10

# Ver estado del sistema
curl http://localhost:8080/quotes/api/stats

# Ejecutar tests
docker-compose exec app ./vendor/bin/pest
```
