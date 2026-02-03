# Laravel Quotes Package

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-007ACC?style=for-the-badge&logo=typescript&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)

**Un paquete Laravel completo para gestionar citas con rate limiting, caché inteligente y UI Vue.js**

[Características](#características) • [Instalación](#instalación) • [Uso](#uso) • [Docker](#docker) • [API](#api-endpoints)

</div>

## 📋 Tabla de Contenidos

- [🎯 Características](#características)
- [🚀 Instalación](#instalación)
- [📖 Uso](#uso)
- [🔧 Configuración](#configuración)
- [🌐 API Endpoints](#api-endpoints)
- [⚙️ Comandos Artisan](#comandos-artisan)
- [🧠 Algoritmo de Búsqueda Binaria](#algoritmo-de-búsqueda-binaria)
- [⏱️ Rate Limiting](#rate-limiting)
- [💻 Interfaz de Usuario](#interfaz-de-usuario)
- [🐳 Docker](#docker)
- [🧪 Testing](#testing)
- [🏗️ Arquitectura](#arquitectura)
- [📁 Estructura del Proyecto](#estructura-del-proyecto)
- [🤝 Contribución](#contribución)
- [📄 Licencia](#licencia)

## 🎯 Características

### ✅ **Completamente Funcional**

- **Rate Limiting Persistente**: 5 solicitudes por 30 segundos (configurable)
- **Caché Inteligente**: Almacenamiento con búsqueda binaria O(log n)
- **Importación Batch**: Comando con reintentos automáticos y unicidad
- **UI Completa**: Vue.js 3 con TypeScript y paginación
- **API RESTful**: Endpoints bien documentados
- **100% Testeado**: Unit tests y feature tests

### 🔥 **Tecnologías Utilizadas**

- **Backend**: Laravel 10+, PHP 8.2+
- **Frontend**: Vue.js 3 (CDN), TypeScript
- **Testing**: PestPHP, Orchestra Testbench
- **Contenedores**: Docker, Docker Compose
- **Algoritmos**: Búsqueda binaria, ordenación

## 🚀 Instalación

### 1. Instalar vía Composer

```bash
composer require vendor/quotes
```

## 🧪 Testing

### Testing

```bash
# Todos los tests
./vendor/bin/pest

# Solo tests unitarios
./vendor/bin/pest tests/Unit

# Solo tests de feature
./vendor/bin/pest tests/Feature
```

🏗️ Arquitectura
Diagrama de Componentes
text
Frontend:
Vue.js UI → Laravel API

Backend:
API → Controller → Service
Service → Cache Store
Service → RateLimiter
Service → BinarySearch
Service → HTTP Client → External API

Console:
BatchImportCommand → Service
Patrones Utilizados
Service Provider: Registro centralizado

Facade: Interfaz simplificada Quote::getQuote()

Repository Pattern: Caché como fuente de datos

Strategy Pattern: Algoritmos intercambiables

Observer Pattern: Eventos de rate limiting

📁 Estructura del Proyecto
text
vendor/quotes/
├── src/
│ ├── Console/
│ │ └── Commands/
│ │ └── BatchImportQuotesCommand.php
│ ├── Contracts/
│ ├── Exceptions/
│ │ └── RateLimitExceededException.php
│ ├── Facades/
│ │ └── Quote.php
│ ├── Http/
│ │ └── Controllers/
│ │ └── QuoteController.php
│ ├── Providers/
│ │ └── QuoteServiceProvider.php
│ ├── Services/
│ │ ├── QuoteService.php
│ │ └── RateLimiter.php
│ └── Utilities/
│ └── BinarySearch.php
├── config/
│ └── quotes.php
├── database/
│ └── migrations/
├── resources/
│ ├── js/ # Vue.js + TypeScript
│ │ ├── app.ts
│ │ ├── components/
│ │ │ └── QuotesUI.vue
│ │ └── types/
│ │ └── quotes.ts
│ ├── views/
│ │ └── ui.blade.php # Vista principal
│ └── dist/ # Assets compilados
├── routes/
│ └── web.php
├── tests/
│ ├── Unit/
│ │ └── BinarySearchTest.php
│ ├── Feature/
│ │ ├── ApiTest.php
│ │ └── BatchImportCommandTest.php
│ ├── TestCase.php
│ └── Pest.php
├── composer.json
├── vite.config.js
├── tsconfig.json
├── phpunit.xml
├── Dockerfile
├── docker-compose.yml
└── README.md
🤝 Contribución
Fork el repositorio

Crear rama para feature (git checkout -b feature/AmazingFeature)

Commit cambios (git commit -m 'Add AmazingFeature')

Push a la rama (git push origin feature/AmazingFeature)

Abrir Pull Request

Guía de Estilo
PHP: PSR-12

JavaScript/TypeScript: Standard JS

Commits: Conventional Commits

Tests: Escribir tests para nuevas funcionalidades

Workflow de Desarrollo
bash

# 1. Clonar

git clone https://github.com/tuusuario/quotes-package.git

# 2. Instalar dependencias

composer install
npm install

# 3. Ejecutar tests

./vendor/bin/pest

# 4. Desarrollo

# - Escribir código

# - Añadir tests

# - Ejecutar tests

# 5. Build assets (opcional)

npm run build

# 6. Commit y push

git add .
git commit -m "feat: agregar nueva funcionalidad"
git push origin feature/nueva-funcionalidad
📄 Licencia
Este proyecto está licenciado bajo la MIT License. Ver el archivo LICENSE para más detalles.

<div align="center">
¿Preguntas o problemas?
Crear un issue •
Ver ejemplos

⭐ Si te gusta este proyecto, ¡dale una estrella en GitHub!

</div>
📞 Soporte
Problemas Comunes
Rate limiting muy restrictivo: Aumenta QUOTES_REQUEST_LIMIT en .env

Caché no persiste: Verifica driver de cache en config/cache.php

UI no carga: Verifica que la ruta /quotes/ui esté registrada

API externa no responde: Verifica conectividad a https://dummyjson.com

Debugging
bash

# Ver logs de Laravel

tail -f storage/logs/laravel.log

# Ver estado de rate limiting

php artisan tinker

> > > app(\Vendor\Quotes\Services\QuoteService::class)->getRateLimitStatus();

# Ver contenido de caché

> > > Cache::get('quotes_storage');
> > > Performance Tips
> > > Usar Redis para cache: CACHE_DRIVER=redis

Aumentar TTL: QUOTES_CACHE_TTL=86400 (24 horas)

Batch imports grandes: Usar --max-attempts alto

Production: Deshabilitar APP_DEBUG=false

🎉 ¡Gracias por usar Laravel Quotes Package!

text

**Para usar este archivo README.md:**

1. **Copia todo el texto anterior** (incluyendo los backticks de inicio y fin del bloque de código)
2. **Crea un archivo `README.md`** en la raíz de tu proyecto
3. **Pega el contenido** (sin los backticks de inicio y fin del bloque - esos son solo para mostrarte el formato aquí)
4. **Ajusta los enlaces y referencias**:
   - Cambia `tuusuario` por tu usuario de GitHub
   - Cambia `vendor/quotes` por el namespace real de tu paquete
   - Actualiza los ejemplos según sea necesario

El archivo está listo para usar en tu repositorio GitHub.
