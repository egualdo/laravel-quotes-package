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
