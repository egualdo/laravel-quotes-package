# Dockerfile - Versión CORREGIDA
FROM php:8.2-cli

WORKDIR /var/www

# Instalar dependencias
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 1. Crear app Laravel
RUN composer create-project --prefer-dist laravel/laravel laravel-app

WORKDIR /var/www/laravel-app

# 2. Copiar TODO el contenido del paquete (incluyendo composer.json)
COPY . /var/www/quotes-package/

# 3. IMPORTANTE: Configurar repositorio ANTES de require
RUN composer config repositories.quotes-package '{"type": "path", "url": "/var/www/quotes-package", "options": {"symlink": false}}'

# 4. Instalar el paquete usando el nombre de tu composer.json
# Cambia "vendor/quotes" por lo que tengas en tu composer.json
RUN composer require "ely/quotes-package:@dev"

# 5. Publicar configuración
RUN php artisan vendor:publish --tag=quotes-config --force --no-interaction

EXPOSE 8000

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]