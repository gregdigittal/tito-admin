# Build stage: install Composer dependencies from project lock file
FROM composer:2 AS builder
WORKDIR /app

# Copy composer files first for Docker layer caching
COPY composer.json composer.lock ./

# Install dependencies (--no-scripts: artisan does not exist yet; we run package:discover after COPY . .)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts \
    --ignore-platform-req=php

# Copy full application code
COPY . .

# Generate app key if not set, clear caches
RUN php artisan package:discover --ansi || true

# Node stage: compile Vite/Tailwind assets
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json ./
RUN npm install
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY resources/ ./resources/
RUN npm run build

# Runtime stage
FROM php:8.2-cli
RUN apt-get update && apt-get install -y --no-install-recommends \
        zip unzip libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install zip pcntl mbstring xml \
    && rm -rf /var/lib/apt/lists/*

COPY --from=builder /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build
WORKDIR /var/www/html

# Ensure storage directories exist with correct permissions
# Note: /bin/sh (dash) does not support brace expansion — spell out each path
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Render sets PORT; default 8080 for local
ENV PORT=8080
EXPOSE 8080
CMD php artisan serve --host=0.0.0.0 --port=${PORT}
